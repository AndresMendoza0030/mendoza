<?php

namespace App\Http\Livewire;

use App\InventoryAlert;
use App\InventoryAnalytics;
use Livewire\Component;
use Livewire\WithPagination;

class AlertasController extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $meses = 6;
    public $perPage = 15;
    public $excluirAjustes = true;

    public $modoAlerta = 'pedidos';
    public $umbralPedidos = 2;
    public $umbralCobertura = 1;
    public $stockMinimo = 0;

    public $pedidosObjetivo = 4;
    public $mesesObjetivo = 2;

    public $mostrarPorNecesidad = false;
    public $umbralNecesidad = 2;

    protected $queryString = [
        'search' => ['except' => ''],
        'meses' => ['except' => 6],
        'perPage' => ['except' => 15],
        'excluirAjustes' => ['except' => true],
        'modoAlerta' => ['except' => 'pedidos'],
        'umbralPedidos' => ['except' => 2],
        'umbralCobertura' => ['except' => 1],
        'stockMinimo' => ['except' => 0],
        'pedidosObjetivo' => ['except' => 4],
        'mesesObjetivo' => ['except' => 2],
        'mostrarPorNecesidad' => ['except' => false],
        'umbralNecesidad' => ['except' => 2],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value)
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $alertas = new InventoryAlert(new InventoryAnalytics('movdetails'));

        $config = $alertas->normalizeConfig([
            'search' => $this->search,
            'meses' => $this->meses,
            'perPage' => $this->perPage,
            'excluirAjustes' => $this->excluirAjustes,
            'modoAlerta' => $this->modoAlerta,
            'umbralPedidos' => $this->umbralPedidos,
            'umbralCobertura' => $this->umbralCobertura,
            'stockMinimo' => $this->stockMinimo,
            'pedidosObjetivo' => $this->pedidosObjetivo,
            'mesesObjetivo' => $this->mesesObjetivo,
            'mostrarPorNecesidad' => $this->mostrarPorNecesidad,
            'umbralNecesidad' => $this->umbralNecesidad,
        ]);

        $allRows = $alertas->getAlertRows($config);
        $summary = $alertas->buildSummary($allRows);
        $products = $alertas->paginateRows($allRows, (int) $this->perPage, (int) $this->page);
        $bodegaPrincipal = $alertas->getPrimaryWarehouse();

        return view('livewire.alertas-controller', [
            'products' => $products,
            'rows' => collect($products->items()),
            'summary' => $summary,
            'bodegaPrincipal' => $bodegaPrincipal,
            'config' => $config,
        ]);
    }
}