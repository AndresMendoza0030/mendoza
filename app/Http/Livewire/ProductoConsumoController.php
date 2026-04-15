<?php

namespace App\Http\Livewire;

use App\InventoryProductStats;
use Livewire\Component;

class ProductoConsumoController extends Component
{
    public $search = '';
    public $productoId = '';
    public $meses = 12;
    public $excluirAjustes = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'productoId' => ['except' => ''],
        'meses' => ['except' => 12],
        'excluirAjustes' => ['except' => true],
    ];

    public function render()
    {
        $stats = new InventoryProductStats();
        $bodegaPrincipal = $stats->getPrimaryWarehouse();
        $products = $stats->getProducts($this->search);

        $productInfo = null;
        $summary = null;
        $monthly = collect();
        $recentMovements = collect();

        if (!empty($this->productoId)) {
            $productInfo = $stats->getProductInfo((int) $this->productoId, (int) $bodegaPrincipal->id);

            if ($productInfo) {
                $summary = $stats->getSummary(
                    (int) $this->productoId,
                    (int) $bodegaPrincipal->id,
                    (int) $this->meses,
                    (bool) $this->excluirAjustes
                );

                $monthly = $stats->getMonthlyConsumption(
                    (int) $this->productoId,
                    (int) $bodegaPrincipal->id,
                    (int) $this->meses,
                    (bool) $this->excluirAjustes
                );

                $recentMovements = $stats->getRecentMovements(
                    (int) $this->productoId,
                    (int) $bodegaPrincipal->id,
                    20
                );
            }
        }

        return view('livewire.producto-consumo-controller', [
            'products' => $products,
            'bodegaPrincipal' => $bodegaPrincipal,
            'productInfo' => $productInfo,
            'summary' => $summary,
            'monthly' => $monthly,
            'recentMovements' => $recentMovements,
        ]);
    }
}