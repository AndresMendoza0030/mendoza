<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-2">
                        <button class="btn btn-dark" wire:click="setAction(1)">
                            <i class="la la-chevron-left"></i>
                        </button>
                    </div>

                    <div class="col-xl-12 text-center">
                        <h5><b>Detalle de Solicitud para Despacho</b></h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-3 col-md-4 col-sm-12">
                    <h7 class="text-info">Bodega Despacha:</h7>
                    <div class="input-group input-group-sm">
                        <input type="text" disabled wire:model.lazy="despachabod" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12">
                    <h7 class="text-info">Destino:</h7>
                    <div class="input-group input-group-sm">
                        <input type="text" disabled wire:model.lazy="eldestino" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12">
                    <h7 class="text-info">Fecha Solicitud y Solicitante:</h7>
                    <div class="input-group input-group-sm">
                        <input type="text" disabled wire:model.lazy="lafechadoc" class="form-control">
                    </div>
                </div>
            </div>

            <div class="table-responsive-sm mt-2">
                <table id="tbdetalle"
                    class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>
                            <th class="text-center ">Descripción de Producto</th>
                            <th class="text-center ">Cantidad</th>
                            <th class="text-center ">Stock Actual</th>
                            <th class="text-center ">ACCIONES</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($detalle as $d)
                            <tr>
                                <td class="text-center">{{ $d->producto_id }} - {{ $d->elproducto }}</td>
                                <td class="text-center">{{ $d->cantidad }}</td>
                                <td class="text-center">{{ $d->stock }}
                                    @if ($d->stock >= $d->cantidad && $d->movimiento == 'S')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-check-circle">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                    @else
                                        @if ($d->movimiento == 'E')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-check-circle">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-x-circle">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="15" y1="9" x2="9" y2="15">
                                                </line>
                                                <line x1="9" y1="9" x2="15" y2="15">
                                                </line>
                                            </svg>
                                            {{ $x = $x + 1 }}
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" onclick="Confirm('{{ $d->id }}')"
                                                data-toggle="tooltip" data-placement="top" title="Quitar Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="purple" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-x-square">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"
                                                        ry="2"></rect>
                                                    <line x1="9" y1="9" x2="15"
                                                        y2="15"></line>
                                                    <line x1="15" y1="9" x2="9"
                                                        y2="15"></line>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($x == 0)
                <div class="row">
                    <div id="botonGrabar" class="row justify-content-between ml-1">
                        <div class="col-md-4 col-lg-4 col-sm-12">
                            <button id="btgrabar" wire:click="grabaDespacho()"
                                class="btn btn-danger mt-4">Despachar</button>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
</div>

@livewireScripts
<script type="text/javascript"></script>
