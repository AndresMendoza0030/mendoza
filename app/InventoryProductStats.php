<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryProductStats
{
    public function getPrimaryWarehouse(): object
    {
        $warehouse = DB::table('warehouses')
            ->select('id', 'nombre')
            ->where('nombre', 'like', '%OFICINA%')
            ->orWhere('nombre', 'like', '%CENTRAL%')
            ->orderBy('id')
            ->first();

        if (!$warehouse) {
            $warehouse = DB::table('warehouses')
                ->select('id', 'nombre')
                ->orderBy('id')
                ->first();
        }

        if (!$warehouse) {
            return (object) [
                'id' => 1,
                'nombre' => 'Oficina Central',
            ];
        }

        return $warehouse;
    }

    public function getProducts(string $search = ''): Collection
    {
        return DB::table('products')
            ->select('id', 'nombre')
            ->when(trim($search) !== '', function ($query) use ($search) {
                $term = '%' . trim($search) . '%';

                $query->where(function ($sub) use ($term) {
                    $sub->where('nombre', 'like', $term)
                        ->orWhere('id', 'like', $term);
                });
            })
            ->orderBy('nombre', 'asc')
            ->limit(200)
            ->get();
    }

    public function getProductInfo(int $productId, int $warehouseId): ?object
    {
        return DB::table('products')
            ->leftJoin('productstocks as ps', function ($join) use ($warehouseId) {
                $join->on('products.id', '=', 'ps.producto_id')
                    ->where('ps.bodega', '=', $warehouseId);
            })
            ->select(
                'products.id',
                'products.nombre',
                DB::raw('COALESCE(ps.existencia, 0) as stock_actual')
            )
            ->where('products.id', $productId)
            ->first();
    }

    public function getSummary(int $productId, int $warehouseId, int $months = 12, bool $excludeAdjustments = true): array
    {
        $fromDate = now()->subMonths($months)->startOfDay()->toDateString();

        $baseOut = DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where('movimiento', 'S')
            ->whereDate('fecha', '>=', $fromDate)
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            });

        if ($excludeAdjustments) {
            $baseOut->where(function ($q) {
                $q->whereNull('tipodoc')
                    ->orWhere('tipodoc', '<>', 'SAJU');
            });
        }

        $baseIn = DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where('movimiento', 'E')
            ->whereDate('fecha', '>=', $fromDate)
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            });

        if ($excludeAdjustments) {
            $baseIn->where(function ($q) {
                $q->whereNull('tipodoc')
                    ->orWhere('tipodoc', '<>', 'SAJU');
            });
        }

        $totalOut = round((float) (clone $baseOut)->sum('cantidad'), 2);
        $totalIn = round((float) (clone $baseIn)->sum('cantidad'), 2);
        $avgMonth = round($totalOut / max(1, $months), 2);

        $monthsWithConsumption = (clone $baseOut)
            ->selectRaw("COUNT(DISTINCT DATE_FORMAT(fecha, '%Y-%m')) as total")
            ->value('total');

        $movementCount = (clone $baseOut)->count();

        $lastConsumption = DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where('movimiento', 'S')
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            })
            ->orderBy('fecha', 'desc')
            ->first();

        $lastPurchase = DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where('movimiento', 'E')
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            })
            ->orderBy('fecha', 'desc')
            ->first();

        return [
            'total_salidas' => $totalOut,
            'total_entradas' => $totalIn,
            'promedio_mensual' => $avgMonth,
            'meses_con_consumo' => (int) ($monthsWithConsumption ?? 0),
            'movimientos_salida' => (int) $movementCount,
            'ultimo_consumo_fecha' => $lastConsumption && !empty($lastConsumption->fecha)
                ? date('d/m/Y', strtotime($lastConsumption->fecha))
                : null,
            'ultimo_consumo_cantidad' => $lastConsumption ? round((float) $lastConsumption->cantidad, 2) : null,
            'ultima_compra_fecha' => $lastPurchase && !empty($lastPurchase->fecha)
                ? date('d/m/Y', strtotime($lastPurchase->fecha))
                : null,
            'ultima_compra_cantidad' => $lastPurchase ? round((float) $lastPurchase->cantidad, 2) : null,
        ];
    }

    public function getMonthlyConsumption(int $productId, int $warehouseId, int $months = 12, bool $excludeAdjustments = true): Collection
    {
        $fromDate = now()->subMonths($months - 1)->startOfMonth()->toDateString();

        $query = DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where('movimiento', 'S')
            ->whereDate('fecha', '>=', $fromDate)
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            });

        if ($excludeAdjustments) {
            $query->where(function ($q) {
                $q->whereNull('tipodoc')
                    ->orWhere('tipodoc', '<>', 'SAJU');
            });
        }

        $rows = $query
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as periodo")
            ->selectRaw("SUM(cantidad) as total")
            ->selectRaw("COUNT(*) as movimientos")
            ->groupBy('periodo')
            ->orderBy('periodo', 'asc')
            ->get()
            ->keyBy('periodo');

        $result = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');

            $row = $rows->get($key);

            $result->push([
                'periodo' => $key,
                'label' => $this->monthLabelEs($date->month) . ' ' . $date->year,
                'total' => $row ? round((float) $row->total, 2) : 0,
                'movimientos' => $row ? (int) $row->movimientos : 0,
            ]);
        }

        return $result;
    }

    public function getRecentMovements(int $productId, int $warehouseId, int $limit = 20): Collection
    {
        return DB::table('movdetails')
            ->where('producto_id', $productId)
            ->where('bodega', $warehouseId)
            ->where(function ($q) {
                $q->whereNull('reversado')
                    ->orWhere('reversado', '<>', 'S');
            })
            ->select('fecha', 'movimiento', 'cantidad', 'tipodoc', 'numerodoc')
            ->orderBy('fecha', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'fecha' => !empty($row->fecha) ? date('d/m/Y', strtotime($row->fecha)) : '',
                    'movimiento' => $row->movimiento,
                    'cantidad' => round((float) $row->cantidad, 2),
                    'tipodoc' => $row->tipodoc,
                    'numerodoc' => $row->numerodoc,
                ];
            });
    }

    protected function monthLabelEs(int $month): string
    {
        $labels = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $labels[$month] ?? '';
    }
}