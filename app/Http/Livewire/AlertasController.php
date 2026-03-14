<?php

namespace App\Http\Livewire;

use App\InventoryAnalytics;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AlertasController extends Component
{
    use WithPagination;

    public $search = '';
    public $meses = 6;
    public $mesesObjetivo = 2;
    public $excluirAjustes = true;
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'meses' => ['except' => 6],
        'mesesObjetivo' => ['except' => 2],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMeses()
    {
        $this->resetPage();
    }

    public function updatingMesesObjetivo()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $analytics = new InventoryAnalytics('movdetails');
        $bodegaPrincipal = $this->getPrimaryWarehouse();

        $products = DB::table('products')
            ->leftJoin('productclasses', 'products.clase', '=', 'productclasses.id')
            ->leftJoin('productstocks as ps', function ($join) use ($bodegaPrincipal) {
                $join->on('products.id', '=', 'ps.producto_id')
                    ->where('ps.bodega', '=', $bodegaPrincipal->id);
            })
            ->select(
                'products.id',
                'products.nombre',
                'productclasses.nombre as categoria',
                DB::raw('COALESCE(ps.existencia, 0) as stock_oficial')
            )
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($sub) use ($term) {
                    $sub->where('products.nombre', 'like', $term)
                        ->orWhere('products.id', 'like', $term);
                });
            })
            ->orderBy('products.nombre', 'asc')
            ->paginate((int) $this->perPage, ['*'], 'page');

        $rows = collect($products->items())->map(function ($product) use ($analytics, $bodegaPrincipal) {
            return $this->buildRow($product, $analytics, (int) $bodegaPrincipal->id);
        });

        $summary = $this->buildSummary($rows, $products);

        return view('livewire.alertas-controller', [
            'products' => $products,
            'rows' => $rows,
            'summary' => $summary,
            'bodegaPrincipal' => $bodegaPrincipal,
        ]);
    }

    private function buildSummary(Collection $rows, LengthAwarePaginator $products): array
    {
        return [
            'total' => $products->total(),
            'criticos' => $rows->where('risk_level', 'CRITICO')->count(),
            'altos' => $rows->where('risk_level', 'ALTO')->count(),
            'comprar' => round((float) $rows->sum('compra_sugerida'), 2),
        ];
    }

    private function buildRow($product, InventoryAnalytics $analytics, int $warehouseId): object
    {
        $stockOficial = round((float) ($product->stock_oficial ?? 0), 2);
        $stockKardex = $this->getLastKardexStock((int) $product->id, $warehouseId);
        $comprometido = $this->getCommittedStock((int) $product->id, $warehouseId);
        $disponible = round($stockOficial - $comprometido, 2);
        $consumo30 = $analytics->consumoPeriodoDias((int) $product->id, $warehouseId, 30, (bool) $this->excluirAjustes);
        $consumo90 = $analytics->consumoPeriodoDias((int) $product->id, $warehouseId, 90, (bool) $this->excluirAjustes);
        $consumoMensual = $analytics->consumoMensualPromedio((int) $product->id, $warehouseId, (int) $this->meses, (bool) $this->excluirAjustes);
        $pedidoTipico = $analytics->pedidoTipicoInteligente((int) $product->id, $warehouseId, (int) $this->meses, (bool) $this->excluirAjustes);
        $cobertura = $analytics->coberturaMeses(max(0, $disponible), $consumoMensual);
        $compraSugerida = $analytics->compraSugerida(max(0, $disponible), $consumoMensual, (int) $this->mesesObjetivo);
        $diferencia = round($stockOficial - $stockKardex, 2);

        [$riskLevel, $riskClass, $reason] = $this->classifyRisk(
            $stockOficial,
            $disponible,
            $consumoMensual,
            $cobertura,
            $diferencia,
            $comprometido,
            $consumo30,
            $consumo90
        );

        return (object) [
            'id' => $product->id,
            'nombre' => $product->nombre,
            'categoria' => $product->categoria ?? '',
            'stock_oficial' => $stockOficial,
            'stock_kardex' => $stockKardex,
            'diferencia' => $diferencia,
            'comprometido' => $comprometido,
            'disponible' => $disponible,
            'consumo_30' => round($consumo30, 2),
            'consumo_90' => round($consumo90, 2),
            'consumo_mes' => round($consumoMensual, 2),
            'pedido_tipico' => round($pedidoTipico, 2),
            'cobertura' => $cobertura,
            'compra_sugerida' => $compraSugerida,
            'risk_level' => $riskLevel,
            'risk_class' => $riskClass,
            'reason' => $reason,
        ];
    }

    private function classifyRisk(
        float $stockOficial,
        float $disponible,
        float $consumoMensual,
        float $cobertura,
        float $diferencia,
        float $comprometido,
        float $consumo30,
        float $consumo90
    ): array {
        if ($stockOficial <= 0 && $consumoMensual > 0) {
            return ['CRITICO', 'danger', 'Sin existencia oficial y sí tiene consumo'];
        }

        if ($disponible <= 0 && ($comprometido > 0 || $consumoMensual > 0)) {
            return ['CRITICO', 'danger', 'El stock disponible ya no cubre los compromisos'];
        }

        if (abs($diferencia) >= 5) {
            return ['ALTO', 'warning', 'Diferencia relevante entre stock oficial y kardex'];
        }

        if ($consumoMensual > 0 && $cobertura < 1) {
            return ['ALTO', 'warning', 'Cobertura menor a 1 mes'];
        }

        if ($consumoMensual > 0 && $cobertura < 2) {
            return ['MEDIO', 'warning', 'Cobertura menor a 2 meses'];
        }

        if ($consumo90 > 0 && $consumo30 > (($consumo90 / 3) * 1.5)) {
            return ['MEDIO', 'info', 'El consumo del último mes viene acelerado'];
        }

        if ($comprometido > 0) {
            return ['MEDIO', 'info', 'Tiene solicitudes autorizadas pendientes'];
        }

        return ['ESTABLE', 'success', 'Comportamiento estable con la ventana actual'];
    }

    private function getPrimaryWarehouse(): object
    {
        $warehouse = DB::table('warehouses')
            ->select('id', 'nombre')
            ->where('nombre', 'like', '%OFICINA%')
            ->orWhere('nombre', 'like', '%CENTRAL%')
            ->orderBy('id')
            ->first();

        if (!$warehouse) {
            $warehouse = DB::table('warehouses')->select('id', 'nombre')->orderBy('id')->first();
        }

        if (!$warehouse) {
            return (object) ['id' => 1, 'nombre' => 'Oficina Central'];
        }

        return $warehouse;
    }

    private function getLastKardexStock(int $productoId, int $bodegaId): float
    {
        $last = DB::table('movdetails')
            ->where('producto_id', $productoId)
            ->where('bodega', $bodegaId)
            ->where(function ($w) {
                $w->whereNull('reversado')->orWhere('reversado', '!=', 'S');
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->value('posterior');

        return round((float) ($last ?? 0), 2);
    }

    private function getCommittedStock(int $productoId, int $bodegaId): float
    {
        try {
            return round((float) DB::table('detareqs as d')
                ->join('reqhead as h', 'h.id', '=', 'd.numerodoc')
                ->where('d.producto_id', $productoId)
                ->where('h.bodega', $bodegaId)
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
}
