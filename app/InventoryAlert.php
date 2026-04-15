<?php

namespace App;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryAlert
{
    /** @var InventoryAnalytics */
    protected $analytics;

    public function __construct(?InventoryAnalytics $analytics = null)
    {
        $this->analytics = $analytics ?: new InventoryAnalytics('movdetails');
    }

    public function normalizeConfig(array $input = []): array
    {
        $modo = $input['modoAlerta'] ?? 'pedidos';

        if (!in_array($modo, ['pedidos', 'cobertura', 'minimo', 'mixto'], true)) {
            $modo = 'pedidos';
        }

        return [
            'search'                 => trim((string) ($input['search'] ?? '')),
            'meses'                  => max(1, (int) ($input['meses'] ?? 6)),
            'perPage'                => max(5, (int) ($input['perPage'] ?? 15)),
            'excluirAjustes'         => filter_var($input['excluirAjustes'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'modoAlerta'             => $modo,
            'umbralPedidos'          => max(0, (float) ($input['umbralPedidos'] ?? 2)),
            'umbralCobertura'        => max(0, (float) ($input['umbralCobertura'] ?? 1)),
            'stockMinimo'            => max(0, (float) ($input['stockMinimo'] ?? 0)),
            'pedidosObjetivo'        => max(1, (float) ($input['pedidosObjetivo'] ?? 4)),
            'mesesObjetivo'          => max(1, (int) ($input['mesesObjetivo'] ?? 2)),
            'mostrarPorNecesidad'    => filter_var($input['mostrarPorNecesidad'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'umbralNecesidad'        => max(0, (int) ($input['umbralNecesidad'] ?? 2)),
        ];
    }

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

    public function getAlertRows(array $config): Collection
    {
        $warehouse = $this->getPrimaryWarehouse();

        $products = DB::table('products')
            ->leftJoin('productstocks as ps', function ($join) use ($warehouse) {
                $join->on('products.id', '=', 'ps.producto_id')
                    ->where('ps.bodega', '=', $warehouse->id);
            })
            ->select(
                'products.id',
                'products.nombre',
                DB::raw('COALESCE(ps.existencia, 0) as stock_oficial')
            )
            ->when($config['search'] !== '', function ($query) use ($config) {
                $term = '%' . $config['search'] . '%';

                $query->where(function ($sub) use ($term) {
                    $sub->where('products.nombre', 'like', $term)
                        ->orWhere('products.id', 'like', $term);
                });
            })
            ->orderBy('products.nombre', 'asc')
            ->get();

        return collect($products)
            ->map(function ($product) use ($warehouse, $config) {
                return $this->buildRow($product, (int) $warehouse->id, $config);
            })
            ->filter(function ($row) use ($config) {
                return $this->shouldAlert($row, $config);
            })
            ->values();
    }

    public function paginateRows(Collection $rows, int $perPage, int $page = 1): LengthAwarePaginator
    {
        $perPage = max(5, $perPage);
        $page = max(1, $page);

        $items = $rows->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function buildSummary(Collection $rows): array
    {
        return [
            'total' => $rows->count(),
            'criticos' => $rows->where('risk_level', 'CRITICO')->count(),
            'altos' => $rows->where('risk_level', 'ALTO')->count(),
            'comprar' => round((float) $rows->sum('compra_sugerida'), 2),
        ];
    }

    protected function buildRow($product, int $warehouseId, array $config): array
    {
        $stockOficial = round((float) ($product->stock_oficial ?? 0), 2);
        $comprometido = $this->getCommittedStock((int) $product->id, $warehouseId);
        $disponibleReal = round($stockOficial - $comprometido, 2);
        $disponibleCalculo = max(0, $disponibleReal);

        $consumoMensualReal = $this->analytics->consumoMensualPromedio(
            (int) $product->id,
            $warehouseId,
            (int) $config['meses'],
            (bool) $config['excluirAjustes']
        );

        $consumoMensualRedondeado = $this->roundHalfUpToInt($consumoMensualReal);

        $pedidoTipico = $this->analytics->pedidoTipicoInteligente(
            (int) $product->id,
            $warehouseId,
            (int) $config['meses'],
            (bool) $config['excluirAjustes']
        );

        $cobertura = $this->analytics->coberturaMeses($disponibleCalculo, $consumoMensualRedondeado);
        $ultimaFechaPedido = $this->getLastOrderDate((int) $product->id, $warehouseId);

        $pedidosRestantes = 999.0;
        if ($pedidoTipico > 0) {
            $pedidosRestantes = round($disponibleCalculo / $pedidoTipico, 2);
        }

        $esPorNecesidad = $consumoMensualRedondeado > 0 && $consumoMensualRedondeado <= (int) $config['umbralNecesidad'];

        $riskLevel = $this->resolveRiskLevel(
            $disponibleReal,
            $cobertura,
            $pedidosRestantes,
            $esPorNecesidad
        );

        $reason = $this->resolveAlertReason(
            $disponibleReal,
            $consumoMensualRedondeado,
            $pedidoTipico,
            $cobertura,
            $pedidosRestantes,
            $config,
            $esPorNecesidad
        );

        $compraSugerida = $this->resolveSuggestedPurchase(
            $disponibleCalculo,
            $consumoMensualRedondeado,
            $pedidoTipico,
            $config
        );

        return [
            'id' => (int) $product->id,
            'nombre' => (string) $product->nombre,
            'stock_oficial' => $stockOficial,
            'comprometido' => $comprometido,
            'disponible' => $disponibleReal,
            'consumo_mes_real' => round($consumoMensualReal, 2),
            'consumo_mes' => $consumoMensualRedondeado,
            'pedido_tipico' => round($pedidoTipico, 2),
            'cobertura' => $cobertura,
            'pedidos_restantes' => $pedidosRestantes,
            'risk_level' => $riskLevel,
            'reason' => $reason,
            'compra_sugerida' => $compraSugerida,
            'ultimo_pedido' => $ultimaFechaPedido,
            'es_por_necesidad' => $esPorNecesidad,
            'tipo_consumo' => $esPorNecesidad ? 'POR NECESIDAD' : 'ROTACION',
        ];
    }

    protected function shouldAlert(array $row, array $config): bool
    {
        if ((int) $row['consumo_mes'] <= 0) {
            return false;
        }

        if (!empty($row['es_por_necesidad']) && !$config['mostrarPorNecesidad']) {
            return false;
        }

        if ((float) $row['disponible'] <= 0 && ((int) $row['consumo_mes'] > 0 || (float) $row['comprometido'] > 0)) {
            return true;
        }

        switch ($config['modoAlerta']) {
            case 'pedidos':
                return (float) $row['pedido_tipico'] > 0
                    && (float) $row['pedidos_restantes'] <= (float) $config['umbralPedidos'];

            case 'cobertura':
                return (int) $row['consumo_mes'] > 0
                    && (float) $row['cobertura'] <= (float) $config['umbralCobertura'];

            case 'minimo':
                return (float) $row['disponible'] <= (float) $config['stockMinimo'];

            case 'mixto':
                $byPedidos = (float) $row['pedido_tipico'] > 0
                    && (float) $row['pedidos_restantes'] <= (float) $config['umbralPedidos'];

                $byCobertura = (int) $row['consumo_mes'] > 0
                    && (float) $row['cobertura'] <= (float) $config['umbralCobertura'];

                $byMinimo = (float) $row['disponible'] <= (float) $config['stockMinimo'];

                return $byPedidos || $byCobertura || $byMinimo;
        }

        return false;
    }

    protected function resolveRiskLevel(float $disponible, float $cobertura, float $pedidosRestantes, bool $esPorNecesidad = false): string
    {
        if ($esPorNecesidad) {
            if ($disponible <= 0 || $pedidosRestantes <= 2 || $cobertura <= 2) {
                return 'ALTO';
            }

            return 'MEDIO';
        }

        if ($disponible <= 0) {
            return 'CRITICO';
        }

        if ($pedidosRestantes <= 1 || $cobertura <= 1) {
            return 'CRITICO';
        }

        if ($pedidosRestantes <= 2 || $cobertura <= 2) {
            return 'ALTO';
        }

        return 'MEDIO';
    }

    protected function resolveAlertReason(
        float $disponible,
        float $consumoMensual,
        float $pedidoTipico,
        float $cobertura,
        float $pedidosRestantes,
        array $config,
        bool $esPorNecesidad = false
    ): string {
        if ($esPorNecesidad) {
            if ($disponible <= 0) {
                return 'Producto por necesidad sin stock disponible';
            }

            return 'Producto por necesidad; consumo mes ' . number_format($consumoMensual, 0) . ' (máximo ' . (int) $config['umbralNecesidad'] . ')';
        }

        if ($disponible <= 0 && $consumoMensual > 0) {
            return 'Sin stock disponible para cubrir consumo';
        }

        if ($config['modoAlerta'] === 'pedidos') {
            if ($pedidoTipico <= 0) {
                return 'Sin historial suficiente para estimar pedido típico';
            }

            return 'Quedan ' . number_format($pedidosRestantes, 2) . ' pedidos; umbral: ' . number_format((float) $config['umbralPedidos'], 2);
        }

        if ($config['modoAlerta'] === 'cobertura') {
            return 'Cobertura actual de ' . number_format($cobertura, 2) . ' meses; umbral: ' . number_format((float) $config['umbralCobertura'], 2);
        }

        if ($config['modoAlerta'] === 'minimo') {
            return 'Disponible de ' . number_format($disponible, 2) . ' contra mínimo de ' . number_format((float) $config['stockMinimo'], 2);
        }

        $motivos = [];

        if ($pedidoTipico > 0 && $pedidosRestantes <= (float) $config['umbralPedidos']) {
            $motivos[] = 'pedidos restantes ' . number_format($pedidosRestantes, 2);
        }

        if ($consumoMensual > 0 && $cobertura <= (float) $config['umbralCobertura']) {
            $motivos[] = 'cobertura ' . number_format($cobertura, 2) . ' meses';
        }

        if ($disponible <= (float) $config['stockMinimo']) {
            $motivos[] = 'debajo del mínimo';
        }

        return empty($motivos) ? 'Cumple una condición de alerta' : 'Activa por: ' . implode(', ', $motivos);
    }

    protected function resolveSuggestedPurchase(
        float $stockDisponible,
        float $consumoMensual,
        float $pedidoTipico,
        array $config
    ): float {
        if ($config['modoAlerta'] === 'pedidos' || $config['modoAlerta'] === 'mixto') {
            if ($pedidoTipico > 0) {
                $objetivo = $pedidoTipico * (float) $config['pedidosObjetivo'];
                return round(max(0, $objetivo - $stockDisponible), 2);
            }
        }

        if ($consumoMensual > 0) {
            return round(max(0, ($consumoMensual * (int) $config['mesesObjetivo']) - $stockDisponible), 2);
        }

        if ($config['modoAlerta'] === 'minimo') {
            return round(max(0, (float) $config['stockMinimo'] - $stockDisponible), 2);
        }

        return 0.0;
    }

    protected function getCommittedStock(int $productoId, int $warehouseId): float
    {
        try {
            return round((float) DB::table('detareqs as d')
                ->join('reqhead as h', 'h.id', '=', 'd.numerodoc')
                ->where('d.producto_id', $productoId)
                ->where('h.bodega', $warehouseId)
                ->where('h.despachado', 'N')
                ->where(function ($q) {
                    $q->where('h.autorizado', 'S')
                        ->orWhere('d.autorizado', 'S');
                })
                ->sum('d.cantidad'), 2);
        } catch (\Throwable $e) {
            return 0.0;
        }
    }

    protected function getLastOrderDate(int $productoId, int $warehouseId): ?string
    {
        try {
            $row = DB::table('movdetails')
                ->where('producto_id', $productoId)
                ->where('bodega', $warehouseId)
                ->where('movimiento', 'E')
                ->where(function ($q) {
                    $q->whereNull('reversado')
                        ->orWhere('reversado', '<>', 'S');
                })
                ->where(function ($q) {
                    $q->whereNull('tipodoc')
                        ->orWhere('tipodoc', '<>', 'SAJU');
                })
                ->orderBy('fecha', 'desc')
                ->select('fecha')
                ->first();

            if (!$row || empty($row->fecha)) {
                return null;
            }

            return date('d/m/Y', strtotime($row->fecha));
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function roundHalfUpToInt(float $value): int
    {
        return (int) floor($value + 0.5);
    }
}