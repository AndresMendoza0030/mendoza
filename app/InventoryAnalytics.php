<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryAnalytics
{
    private string $table;

    public function __construct(string $table = 'movdetails')
    {
        $this->table = $table;
    }

    public function consumoMensualPromedio(int $productoId, int $bodegaId, int $meses = 6, bool $excluirAjustes = true): float
    {
        $meses = max(1, $meses);
        $desde = Carbon::now()->startOfDay()->subMonths($meses);

        $q = DB::table($this->table)
            ->where('producto_id', $productoId)
            ->where('bodega', $bodegaId)
            ->where('movimiento', 'S')
            ->whereDate('fecha', '>=', $desde)
            ->where(function ($w) {
                $w->whereNull('reversado')->orWhere('reversado', '!=', 'S');
            });

        if ($excluirAjustes) {
            $q->where('tipodoc', '!=', 'SAJU');
        }

        $total = (float) $q->sum('cantidad');
        return round($total / $meses, 2);
    }

    public function consumoPeriodoDias(int $productoId, int $bodegaId, int $dias = 30, bool $excluirAjustes = true): float
    {
        $dias = max(1, $dias);
        $desde = Carbon::now()->startOfDay()->subDays($dias);

        $q = DB::table($this->table)
            ->where('producto_id', $productoId)
            ->where('bodega', $bodegaId)
            ->where('movimiento', 'S')
            ->whereDate('fecha', '>=', $desde)
            ->where(function ($w) {
                $w->whereNull('reversado')->orWhere('reversado', '!=', 'S');
            });

        if ($excluirAjustes) {
            $q->where('tipodoc', '!=', 'SAJU');
        }

        return (float) $q->sum('cantidad');
    }

    public function tamanosPedido(int $productoId, int $bodegaId, int $meses = 6, bool $excluirAjustes = true): array
    {
        $desde = Carbon::now()->startOfDay()->subMonths(max(1, $meses));

        $q = DB::table($this->table)
            ->selectRaw('SUM(cantidad) as qty')
            ->where('producto_id', $productoId)
            ->where('bodega', $bodegaId)
            ->where('movimiento', 'S')
            ->whereDate('fecha', '>=', $desde)
            ->where(function ($w) {
                $w->whereNull('reversado')->orWhere('reversado', '!=', 'S');
            })
            ->groupBy('tipodoc', 'numerodoc', 'fecha', 'bodega');

        if ($excluirAjustes) {
            $q->where('tipodoc', '!=', 'SAJU');
        }

        $rows = $q->get();
        $sizes = [];
        foreach ($rows as $r) {
            $v = (float) $r->qty;
            if ($v > 0) {
                $sizes[] = $v;
            }
        }

        sort($sizes);
        return $sizes;
    }

    public function pedidoTipicoInteligente(int $productoId, int $bodegaId, int $meses = 6, bool $excluirAjustes = true): float
    {
        $consumoMes = $this->consumoMensualPromedio($productoId, $bodegaId, $meses, $excluirAjustes);
        $sizes = $this->tamanosPedido($productoId, $bodegaId, $meses, $excluirAjustes);

        if (count($sizes) === 0) {
            return 0.0;
        }

        if ($consumoMes > 0) {
            $min = max(1.0, $consumoMes / 4.0);
            $filtered = array_values(array_filter($sizes, function ($x) use ($min) {
                return $x >= $min;
            }));

            if (count($filtered) >= max(3, (int) floor(count($sizes) * 0.4))) {
                $sizes = $filtered;
            }
        }

        $median = $this->percentil($sizes, 0.50);
        $p75 = $this->percentil($sizes, 0.75);

        return round(max($median, $p75 * 0.80), 2);
    }

    public function coberturaMeses(float $stockActual, float $consumoMensual): float
    {
        if ($consumoMensual <= 0) {
            return 999.0;
        }

        return round($stockActual / $consumoMensual, 2);
    }

    public function compraSugerida(float $stockDisponible, float $consumoMensual, int $mesesObjetivo): float
    {
        $necesario = $consumoMensual * max(1, $mesesObjetivo);
        return round(max(0.0, $necesario - $stockDisponible), 2);
    }

    private function percentil(array $sorted, float $p): float
    {
        $n = count($sorted);
        if ($n === 0) {
            return 0.0;
        }

        if ($n === 1) {
            return (float) $sorted[0];
        }

        $p = min(1.0, max(0.0, $p));
        $idx = ($n - 1) * $p;
        $lo = (int) floor($idx);
        $hi = (int) ceil($idx);

        $a = (float) $sorted[$lo];
        $b = (float) $sorted[$hi];

        if ($lo === $hi) {
            return $a;
        }

        $t = $idx - $lo;
        return $a + ($b - $a) * $t;
    }
}
