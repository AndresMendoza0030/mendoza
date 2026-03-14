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
                        <h5>Detalle del documento para :<b>{{ $xdocumento }}</b></h5>
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
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
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
                @if ($listo == 0)
                    <div class="form-group col-lg-2 col-md-6 col-sm-12 mt-3">
                        <button onclick=confirmaDespacho("TOTAL") type="button"
                            class="btn btn-sm btn-warning">Despachar Solicitud</button>
                    </div>
                @endif
                @if ($listo == 1)
                    <div class="form-group col-lg-2 col-md-6 col-sm-12 mt-3">
                        <button onclick=confirmaDespacho("PARCIAL") type="button"
                            class="btn btn-sm btn-warning">Despachar Items Autorizados</button>
                    </div>
                @endif
                @if ($listo == 2)
                    <div class="form-group col-lg-2 col-md-6 col-sm-12 mt-3">
                        <button disabled type="button" class="btn btn-sm btn-danger">No Hay Items Autorizados</button>
                    </div>
                @endif

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card text-center">
                        <h7 class="card-header"><b>Detalle de Solicitud</b></h7>
                    </div>
                    <div class="table-responsive-sm ">
                        <table id="tbcargo"
                            class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                            @if ($tipodocumento == 'S')
                                <thead>
                                    <tr>
                                        <th class="text-center col-2">ACCIONES</th>
                                        <th class="text-center col-4">DESCRIPCIÓN</th>
                                        <th class="text-center col-2">SOLICITADO</th>
                                        <th class="text-center col-2">AUTORIZADO</th>
                                        <th class="text-center col-2">STOCK</th>
                                    </tr>
                                </thead>
                            @endif
                            <tbody>
                                @if (count($info) > 0)
                                    @foreach ($info as $d)
                                        <tr>
                                            <td class="text-center">
                                                <ul class="table-controls">
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                            onclick="Confirm('{{ $d['reg'] }}','{{ $d['producto'] }}')"
                                                            data-toggle="tooltip" data-placement="top"
                                                            title="Quitar Item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="red" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="feather feather-x-square">
                                                                <rect x="3" y="3" width="18" height="18"
                                                                    rx="2" ry="2"></rect>
                                                                <line x1="9" y1="9" x2="15"
                                                                    y2="15"></line>
                                                                <line x1="15" y1="9" x2="9"
                                                                    y2="15"></line>
                                                            </svg>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                            <td class="text-left">
                                                <b>{{ $d['productoid'] }}</b> - {{ $d['producto'] }}
                                            </td>
                                            <td class="text-center">
                                                {{ $d['solicitado'] }}
                                            </td>
                                            <td class="text-center">
                                                @if ($d['solicitado'] > $d['autorizado'] && $d['autorizado'] > 0)
                                                    <b>
                                                        <FONT COLOR ='orange'>{{ $d['autorizado'] }}</FONT>
                                                    </b>
                                                @endif
                                                @if ($d['autorizado'] == 0)
                                                    <b>
                                                        <FONT COLOR ='red'>{{ $d['autorizado'] }}</FONT>
                                                    </b>
                                                @endif
                                                @if ($d['autorizado'] == $d['solicitado'])
                                                    <b>
                                                        <FONT COLOR ='green'>{{ $d['autorizado'] }}</FONT>
                                                    </b>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $d['stock'] }}
                                                @if ($d['stock'] >= $d['autorizado'])
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none" stroke="green"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check-circle">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@livewireScripts
<script type="text/javascript"></script>
