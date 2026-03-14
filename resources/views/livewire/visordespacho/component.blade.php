<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        @if ($action == 1)
            <div class="widget-content-area br-4">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <h5><b>Visor de Solicitudes a Bodega</b></h5>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-lg-4 col-md-12 col-sm-12 mt-2">
                        <div class="input-group input-group-sm ">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGrouptipomovimiento">Bodega Despacha</label>
                            </div>
                            <select class="form-control" wire:model.lazy="labodega" id="bodega">
                                <option value="">Todos</option>
                                @foreach ($bodegas as $bd)
                                    <option value="{{ $bd->id }}">
                                        {{ $bd->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12 col-sm-12 mt-2">
                        <div class="input-group input-group-sm ">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGrouptipomovimiento">Tipo Solicitud</label>
                            </div>
                            <select class="form-control" wire:model.lazy="tipodoc" id="docus">
                                <option value="">Todos</option>
                                @foreach ($tipomovs as $tm)
                                    <option value="{{ $tm->documento }}">
                                        {{ $tm->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <div class="table-responsive-sm mt-3">
                    <table id="tabladetalle" name= "tabladetalle"
                        class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                        <thead>
                            <tr>
                                <th class="text-center col-1 "
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    SOLICITADO A</th>
                                <th class="text-center col-2 "
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    FECHA/HORA</th>
                                <th class="text-center col-3"
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    TIPO SOLICITIUD</th>
                                <th class="text-center col-3"
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    DESTINO</th>
                                <th class="text-center col-1"
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    SOLICITA POR</th>
                                <th class="text-center col-1"
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    ESTATUS</th>
                                <th class="text-center col-1"
                                    style="--bs-btn-padding-y: .3rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .70rem;">
                                    ACCIONES</th>
                            </tr>
                        </thead>

                        <tbody id="tbdetalle" name="tbdetalle">
                            @foreach ($docum as $r)
                                <tr>
                                    <td class="text-center" style="font-size: .70rem;">{{ $r->cbodega }}</td>
                                    <td class="text-center" style="font-size: .70rem;">
                                        {{ $r->created_at->format('d-m-Y') }} - {{ $r->created_at->toTimeString() }}
                                    </td>
                                    <td class="text-left" style="font-size: .70rem;">{{ $r->documento }}</td>
                                    <td class="text-left" style="font-size: .70rem;">{{ $r->nombrepaciente }}
                                        {{ $r->cbodegadestino }} {{ $r->cdestino }} </td>
                                    <td class="text-center" style="font-size: .70rem;">{{ $r->usuario }}</td>
                                    <td class="text-center" style="font-size: .70rem;">
                                        @if ($r->autorizado == 'S')
                                            <span class="badge badge-success">Autorizada</span>
                                        @else
                                            <span class="badge badge-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);"
                                                    wire:click="verDetalle({{ $r->idm }})" data-toggle="tooltip"
                                                    data-placement="top" title="Despachar Movimiento">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none" stroke="green"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-chevrons-right">
                                                        <polyline points="13 17 18 12 13 7"></polyline>
                                                        <polyline points="6 17 11 12 6 7"></polyline>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);"
                                                    onclick="ConfirmDocu('{{ $r->id }}')" data-toggle="tooltip"
                                                    data-placement="top" title="Quitar Movimiento">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none" stroke="red"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-delete">
                                                        <path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z">
                                                        </path>
                                                        <line x1="18" y1="9" x2="12"
                                                            y2="15"></line>
                                                        <line x1="12" y1="9" x2="18"
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
                    {{ $docum->links() }}
                </div>
            </div>
        @elseif($action == 2)
            @include('livewire.visordespacho.formdespachoprod')
        @endif
    </div>
    @include('livewire.visordespacho.modal')
</div>

@livewireScripts

<script type="text/javascript">
    function confirmaDespacho() {
        swal({
                title: 'CONFIRMAR',
                text: '¿SEGURO DE DESPACHAR SOLICITUD?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            },
            function() {
                window.livewire.emit('confirmaDespacho')
                swal.close()
            })
    }

    function Confirm(id, prod) {
        swal({
                title: 'CONFIRMAR',
                text: '¿DESEAS QUITAR EL PRODUCTO ' + prod + ' DE LA SOLICITUD?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            },
            function() {
                window.livewire.emit('deleteRow', id)
                swal.close()
            })
    }

    function Confirm2(id, prod) {
        swal({
                title: 'CONFIRMAR',
                text: '¿DESEAS QUITAR EL PRODUCTO ' + prod + ' DEL DOCUMENTO A DESPACHAR?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            },
            function() {
                window.livewire.emit('deleteRow2', id)
                swal.close()
            })
    }

    function ConfirmDocu(id) {
        swal({
                title: 'CONFIRMAR',
                text: '¿DESEAS BORRAR EL DOCUMENTO DE SOLICITUD?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            },
            function() {
                window.livewire.emit('deleteDoc', id)

                swal.close()
            })
    }


    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('print-mov', idMov => {
            var wactual = window;
            var ruta = "{{ url('detallemov') }}" + '/' + idMov;
            var w = window.open(ruta, "_blank");
            wactual.focus();
        })

        window.livewire.on('muestraModalProcesar', accion => {
            idprod = 0;
            bdprod = 0;
            xelproducto = '';
            xlacantidad = '';
            conlote = 0;
            xnumreg = 0;
            registrosel = [];
            datoslote = [];

            $('#mdstock').val(accion.stock)
            $('#mdproducto').val(accion.producto)
            $('#mdcantidad').val(accion.cantidad)
            $("#mdtabla tbody tr").remove();
            $('#mdtbody').append(accion.tabla);
            $('#modalDespachoItem').modal('show')
        })

        window.livewire.on('enadisa-docus', accion => {
            if (accion == 1) {
                $('#docus').attr("disabled", "disabled");
            } else {
                $('#docus').removeAttr("disabled");
            }
        })
        window.livewire.on('enadisa-bodega', accion => {
            if (accion == 1) {
                $('#bodega').attr("disabled", "disabled");
            } else {
                $('#bodega').removeAttr("disabled");
            }
        })
    })
</script>
