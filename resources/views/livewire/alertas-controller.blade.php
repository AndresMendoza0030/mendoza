<div class="row layout-top-spacing">
    <div class="col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <style>
                .alertas-panel .panel-title { font-size: 1.05rem; font-weight: 700; margin-bottom: .2rem; }
                .alertas-panel .panel-subtitle { font-size: .78rem; color: #888ea8; }
                .alertas-panel .mini-card {
                    border: 1px solid #e0e6ed;
                    border-radius: 10px;
                    padding: 12px 14px;
                    height: 100%;
                    background: #fff;
                }
                .alertas-panel .mini-label { font-size: .68rem; text-transform: uppercase; color: #888ea8; }
                .alertas-panel .mini-value { font-size: 1.1rem; font-weight: 700; color: #3b3f5c; }
                .alertas-panel .toolbar-card {
                    border: 1px solid #e0e6ed;
                    border-radius: 10px;
                    padding: 12px;
                    margin-top: 14px;
                    margin-bottom: 14px;
                    background: #fff;
                }
                .alertas-panel .table-wrap {
                    width: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                }
                .alertas-panel .inventory-table {
                    min-width: 1280px;
                    margin-bottom: 0;
                }
                .alertas-panel .inventory-table thead th {
                    position: sticky;
                    top: 0;
                    z-index: 1;
                    background: #f8f9fa;
                    font-size: .68rem;
                    text-transform: uppercase;
                    color: #888ea8;
                    white-space: nowrap;
                    padding: .65rem .55rem;
                    border-bottom: 1px solid #e0e6ed;
                }
                .alertas-panel .inventory-table td {
                    font-size: .72rem;
                    vertical-align: middle;
                    padding: .60rem .55rem;
                    white-space: nowrap;
                }
                .alertas-panel .inventory-table td.product-cell {
                    min-width: 270px;
                    white-space: normal;
                }
                .alertas-panel .metric-badge {
                    display: inline-block;
                    min-width: 70px;
                    text-align: center;
                    border-radius: 999px;
                    padding: .25rem .55rem;
                    font-size: .68rem;
                    font-weight: 700;
                }
                .alertas-panel .reason-text {
                    max-width: 240px;
                    white-space: normal;
                    line-height: 1.25;
                    color: #515365;
                }
                /* SECCIÓN DE PAGINACIÓN CORREGIDA */
                .alertas-panel .pagination-wrapper {
                    width: 100%;
                    overflow-x: auto;
                }
                .alertas-panel .pagination-wrapper .pagination {
                    margin-bottom: 0;
                    flex-wrap: wrap; /* Permite que los botones bajen si no hay espacio */
                    gap: 4px; /* Otorga un respiro en móviles */
                    justify-content: center;
                }
                /* FIN SECCIÓN PAGINACIÓN */
                .alertas-panel .form-control,
                .alertas-panel .custom-select {
                    font-size: .75rem;
                    height: calc(1.9em + .75rem + 2px);
                }
                .alertas-panel .input-group-text {
                    font-size: .75rem;
                    padding: .375rem .6rem;
                }
                @media (max-width: 991.98px) {
                    .alertas-panel .inventory-table { min-width: 1180px; }
                }
            </style>

            <div class="alertas-panel">
                <div class="widget-header">
                    <div class="row align-items-center">
                        <div class="col-xl-8 col-lg-7 col-md-12 mb-2">
                            <div class="panel-title">ALERTAS Y ABASTECIMIENTO</div>
                            <div class="panel-subtitle">
                                Bodega analizada: <b>{{ $bodegaPrincipal->nombre }}</b>. Stock oficial desde productstocks e histórico desde movdetails.
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 col-md-12 text-lg-right">
                            <span class="badge badge-light" style="font-size:.72rem;">{{ $summary['total'] }} productos</span>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="mini-card">
                            <div class="mini-label">Críticos en esta página</div>
                            <div class="mini-value text-danger">{{ $summary['criticos'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="mini-card">
                            <div class="mini-label">Altos en esta página</div>
                            <div class="mini-value text-warning">{{ $summary['altos'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="mini-card">
                            <div class="mini-label">Compra sugerida página</div>
                            <div class="mini-value">{{ number_format($summary['comprar'], 0) }}</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="mini-card">
                            <div class="mini-label">Meses objetivo</div>
                            <div class="mini-value">{{ $mesesObjetivo }}</div>
                        </div>
                    </div>
                </div>

                <div class="toolbar-card">
                    <div class="row align-items-end">
                        <div class="col-xl-4 col-lg-6 col-md-12 mb-2">
                            <label class="text-muted mb-1" style="font-size:.68rem;">Buscar producto</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="la la-search"></i></span>
                                </div>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Nombre o ID"
                                    wire:model.live.debounce.500ms="search">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4 mb-2">
                            <label class="text-muted mb-1" style="font-size:.68rem;">Ventana análisis</label>
                            <select class="custom-select" wire:model.live="meses">
                                <option value="3">3 meses</option>
                                <option value="6">6 meses</option>
                                <option value="12">12 meses</option>
                            </select>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4 mb-2">
                            <label class="text-muted mb-1" style="font-size:.68rem;">Cobertura objetivo</label>
                            <input type="number" min="1" max="12" class="form-control" wire:model="mesesObjetivo">
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4 mb-2">
                            <label class="text-muted mb-1" style="font-size:.68rem;">Registros por página</label>
                            <select class="custom-select" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-12 mb-2">
                            <div class="custom-control custom-checkbox mt-4 pt-1">
                                <input type="checkbox" class="custom-control-input" id="excluirAjustes" wire:model.live="excluirAjustes">
                                <label class="custom-control-label" for="excluirAjustes" style="font-size:.72rem;">Excluir ajustes</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-wrap border rounded">
                    <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head inventory-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:72px;">ID</th>
                                <th>Producto</th>
                                <th class="text-center">Riesgo</th>
                                <th class="text-center">Stock oficial</th>
                                <th class="text-center">Kardex</th>
                                <th class="text-center">Dif.</th>
                                <th class="text-center">Comprometido</th>
                                <th class="text-center">Disponible</th>
                                <th class="text-center">Cons. 30d</th>
                                <th class="text-center">Cons. mes</th>
                                <th class="text-center">Cobertura</th>
                                <th class="text-center">Pedido típico</th>
                                <th class="text-center">Compra sug.</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                <tr wire:key="alerta-row-{{ $r->id }}">
                                    <td class="text-center">{{ $r->id }}</td>
                                    <td class="product-cell">
                                        <div><b>{{ $r->nombre }}</b></div>
                                        @if(!empty($r->categoria))
                                            <div class="text-muted" style="font-size:.66rem;">{{ $r->categoria }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $r->risk_class }}">{{ $r->risk_level }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($r->stock_oficial, 0) }}</td>
                                    <td class="text-center">{{ number_format($r->stock_kardex, 0) }}</td>
                                    <td class="text-center">
                                        @if(abs($r->diferencia) >= 5)
                                            <span class="metric-badge badge badge-warning">{{ number_format($r->diferencia, 0) }}</span>
                                        @else
                                            <span>{{ number_format($r->diferencia, 0) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($r->comprometido, 0) }}</td>
                                    <td class="text-center">
                                        @if($r->disponible <= 0)
                                            <span class="metric-badge badge badge-danger">{{ number_format($r->disponible, 0) }}</span>
                                        @elseif($r->disponible < max(1, $r->consumo_mes))
                                            <span class="metric-badge badge badge-warning">{{ number_format($r->disponible, 0) }}</span>
                                        @else
                                            <span class="metric-badge badge badge-success">{{ number_format($r->disponible, 0) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($r->consumo_30, 0) }}</td>
                                    <td class="text-center">{{ number_format($r->consumo_mes, 0) }}</td>
                                    <td class="text-center">
                                        @if($r->cobertura >= 999)
                                            <span class="text-muted">Sin consumo</span>
                                        @elseif($r->cobertura < 1)
                                            <span class="metric-badge badge badge-danger">{{ number_format($r->cobertura, 2) }}</span>
                                        @elseif($r->cobertura < 2)
                                            <span class="metric-badge badge badge-warning">{{ number_format($r->cobertura, 2) }}</span>
                                        @else
                                            <span class="metric-badge badge badge-success">{{ number_format($r->cobertura, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($r->pedido_tipico, 0) }}</td>
                                    <td class="text-center font-weight-bold text-danger">{{ number_format($r->compra_sugerida, 0) }}</td>
                                    <td class="reason-text">{{ $r->reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center text-muted p-4">No se encontraron productos con los filtros actuales.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mt-3 pagination-wrapper">
                    <div class="text-muted mb-2 mb-lg-0" style="font-size:.72rem;">
                        Mostrando {{ $products->firstItem() ?? 0 }} a {{ $products->lastItem() ?? 0 }} de {{ $products->total() }} registros
                    </div>
                    <div>
                       
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>