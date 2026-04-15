<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <h3 class="mb-1">Consumo detallado por producto</h3>
            <p class="text-muted mb-0">
                Bodega analizada: <strong>{{ $bodegaPrincipal->nombre }}</strong>
            </p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-white">
            <strong>Selección</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <label>Buscar producto</label>
                    <input
                        type="text"
                        class="form-control"
                        wire:model.debounce.400ms="search"
                        placeholder="Nombre o ID del producto"
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label>Producto</label>
                    <select class="form-control" wire:model="productoId">
                        <option value="">Seleccione un producto</option>
                        @foreach($products as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->id }} - {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label>Historial</label>
                    <select class="form-control" wire:model="meses">
                        <option value="6">6 meses</option>
                        <option value="12">12 meses</option>
                        <option value="24">24 meses</option>
                    </select>
                </div>

                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="excluirAjustes" id="excluirAjustesProd">
                        <label class="form-check-label" for="excluirAjustesProd">
                            Ajustes
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($productInfo && $summary)
        <div class="row mb-3">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Stock actual</small>
                        <h4 class="mb-0">{{ number_format($productInfo->stock_actual, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Total salidas</small>
                        <h4 class="mb-0">{{ number_format($summary['total_salidas'], 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Promedio mensual</small>
                        <h4 class="mb-0">{{ number_format($summary['promedio_mensual'], 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Meses con consumo</small>
                        <h4 class="mb-0">{{ $summary['meses_con_consumo'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white">
                <strong>Resumen del producto</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Producto:</strong> {{ $productInfo->id }} - {{ $productInfo->nombre }}
                    </div>
                    <div class="col-md-3 mb-2">
                        <strong>Total entradas:</strong> {{ number_format($summary['total_entradas'], 2) }}
                    </div>
                    <div class="col-md-3 mb-2">
                        <strong>Movimientos de salida:</strong> {{ $summary['movimientos_salida'] }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Último consumo:</strong>
                        {{ $summary['ultimo_consumo_fecha'] ?: 'Sin registro' }}
                        @if(!is_null($summary['ultimo_consumo_cantidad']))
                            ({{ number_format($summary['ultimo_consumo_cantidad'], 2) }})
                        @endif
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Última compra/entrada:</strong>
                        {{ $summary['ultima_compra_fecha'] ?: 'Sin registro' }}
                        @if(!is_null($summary['ultima_compra_cantidad']))
                            ({{ number_format($summary['ultima_compra_cantidad'], 2) }})
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white">
                <strong>Consumo por mes</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Periodo</th>
                                <th>Consumo total</th>
                                <th>Movimientos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthly as $m)
                                <tr>
                                    <td>{{ $m['label'] }}</td>
                                    <td>{{ number_format($m['total'], 2) }}</td>
                                    <td>{{ $m['movimientos'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <strong>Últimos movimientos</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Movimiento</th>
                                <th>Cantidad</th>
                                <th>Tipo Doc</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $mov)
                                <tr>
                                    <td>{{ $mov['fecha'] }}</td>
                                    <td>
                                        @if($mov['movimiento'] === 'S')
                                            <span class="badge badge-warning">SALIDA</span>
                                        @else
                                            <span class="badge badge-success">ENTRADA</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($mov['cantidad'], 2) }}</td>
                                    <td>{{ $mov['tipodoc'] ?: '-' }}</td>
                                    <td>{{ $mov['numerodoc'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No hay movimientos para este producto.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(!empty($productoId))
        <div class="alert alert-warning">
            No se encontró información para el producto seleccionado.
        </div>
    @else
        <div class="alert alert-light border">
            Selecciona un producto para ver sus estadísticas de consumo.
        </div>
    @endif
</div>