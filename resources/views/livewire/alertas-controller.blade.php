<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">Alertas de desabastecimiento</h3>
                    <p class="text-muted mb-0">
                        Bodega analizada: <strong>{{ $bodegaPrincipal->nombre }}</strong>
                    </p>
                </div>

                <div class="mt-2 mt-md-0">
                    <a
                        href="{{ route('inventario.alertas.pdf', [
                            'search' => $search,
                            'meses' => $meses,
                            'excluirAjustes' => $excluirAjustes ? 1 : 0,
                            'modoAlerta' => $modoAlerta,
                            'umbralPedidos' => $umbralPedidos,
                            'umbralCobertura' => $umbralCobertura,
                            'stockMinimo' => $stockMinimo,
                            'pedidosObjetivo' => $pedidosObjetivo,
                            'mesesObjetivo' => $mesesObjetivo,
                            'mostrarPorNecesidad' => $mostrarPorNecesidad ? 1 : 0,
                            'umbralNecesidad' => $umbralNecesidad,
                        ]) }}"
                        target="_blank"
                        class="btn btn-danger"
                    >
                        Generar PDF de compra sugerida
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Productos en alerta</small>
                    <h4 class="mb-0">{{ $summary['total'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Críticos</small>
                    <h4 class="mb-0 text-danger">{{ $summary['criticos'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Altos</small>
                    <h4 class="mb-0 text-warning">{{ $summary['altos'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Total sugerido a comprar</small>
                    <h4 class="mb-0 text-primary">{{ number_format($summary['comprar'], 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-white">
            <strong>Configuración de alertas</strong>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Buscar producto</label>
                    <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="Nombre o ID">
                </div>

                <div class="col-md-2 mb-3">
                    <label>Historial</label>
                    <select class="form-control" wire:model="meses">
                        <option value="3">3 meses</option>
                        <option value="6">6 meses</option>
                        <option value="12">12 meses</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label>Registros por página</label>
                    <select class="form-control" wire:model="perPage">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Método de alerta</label>
                    <select class="form-control" wire:model="modoAlerta">
                        <option value="pedidos">Por pedidos restantes</option>
                        <option value="cobertura">Por cobertura en meses</option>
                        <option value="minimo">Por stock mínimo fijo</option>
                        <option value="mixto">Mixto</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Umbral de pedidos restantes</label>
                    <input type="number" min="0" step="0.1" class="form-control" wire:model="umbralPedidos">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Umbral de cobertura</label>
                    <input type="number" min="0" step="0.1" class="form-control" wire:model="umbralCobertura">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Stock mínimo</label>
                    <input type="number" min="0" step="0.1" class="form-control" wire:model="stockMinimo">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Meses objetivo de compra</label>
                    <input type="number" min="1" step="1" class="form-control" wire:model="mesesObjetivo">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Pedidos objetivo para reponer</label>
                    <input type="number" min="1" step="0.1" class="form-control" wire:model="pedidosObjetivo">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Consumo máximo "por necesidad"</label>
                    <input type="number" min="1" step="1" class="form-control" wire:model="umbralNecesidad">
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="excluirAjustes" id="excluirAjustes">
                        <label class="form-check-label" for="excluirAjustes">
                            Excluir ajustes
                        </label>
                    </div>
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="mostrarPorNecesidad" id="mostrarPorNecesidad">
                        <label class="form-check-label" for="mostrarPorNecesidad">
                            Mostrar productos por necesidad
                        </label>
                    </div>
                </div>
            </div>

            <div class="alert alert-light border mb-0">
                <strong>Nota:</strong>
                el consumo mes se redondea a entero. Si queda en <strong>0</strong>, se excluye.
                Si queda entre <strong>1</strong> y <strong>{{ $umbralNecesidad }}</strong>, se considera
                <strong>por necesidad</strong>: su aparición es opcional y como máximo puede quedar en <strong>ALTO</strong>.
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Productos con alerta</strong>
            <span class="text-muted">Mostrando {{ $products->firstItem() ?? 0 }} a {{ $products->lastItem() ?? 0 }} de {{ $products->total() }}</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Riesgo</th>
                            <th>Disponible</th>
                            <th>Consumo mes</th>
                            <th>Pedido típico</th>
                            <th>Último pedido</th>
                            <th>Pedidos restantes</th>
                            <th>Cobertura</th>
                            <th>Compra sugerida</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td>{{ $r['id'] }}</td>
                                <td><strong>{{ $r['nombre'] }}</strong></td>
                                <td>
                                    @if(!empty($r['es_por_necesidad']))
                                        <span class="badge badge-secondary">POR NECESIDAD</span>
                                    @else
                                        <span class="badge badge-primary">ROTACIÓN</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r['risk_level'] === 'CRITICO')
                                        <span class="badge badge-danger">CRÍTICO</span>
                                    @elseif($r['risk_level'] === 'ALTO')
                                        <span class="badge badge-warning">ALTO</span>
                                    @else
                                        <span class="badge badge-info">MEDIO</span>
                                    @endif
                                </td>
                                <td class="{{ $r['disponible'] <= 0 ? 'text-danger font-weight-bold' : '' }}">
                                    {{ number_format($r['disponible'], 0) }}
                                </td>
                                <td>{{ $r['consumo_mes'] }}</td>
                                <td>{{ number_format($r['pedido_tipico'], 2) }}</td>
                                <td>{{ $r['ultimo_pedido'] ?: 'Sin registro' }}</td>
                                <td>
                                    @if($r['pedidos_restantes'] >= 999)
                                        N/D
                                    @else
                                        {{ number_format($r['pedidos_restantes'], 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($r['cobertura'] >= 999)
                                        Sin consumo
                                    @else
                                        {{ number_format($r['cobertura'], 2) }} meses
                                    @endif
                                </td>
                                <td class="font-weight-bold text-primary">
                                    {{ number_format($r['compra_sugerida'], 0) }}
                                </td>
                                <td>{{ $r['reason'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">
                                    No se encontraron productos en alerta con la configuración actual.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($products->total() > 0)
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>