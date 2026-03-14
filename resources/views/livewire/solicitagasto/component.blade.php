<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <!-- Título -->
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Solicitar Producto para Gasto/Consumo</b></h5>
                    </div>
                </div>
            </div>

            <!-- 1a Fila: Fecha, Concepto, Bodega, Destino -->
            <div class="row mt-1">
                <!-- Fecha -->
                <div class="form-group col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Fecha</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-calendar la-lg"></i>
                            </div>
                        </div>
                        <input type="date" tabindex=1 wire:model.defer="lafecha" class="form-control">
                    </div>
                </div>

                <!-- Concepto -->
                <div class="form-group col-lg-4 col-md-8 col-sm-12">
                    <h7 class="text-info">Concepto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-edit la-lg"></i>
                            </div>
                        </div>
                        <input type="text" tabindex=2 wire:model.defer="concepto"
                            wire:change="asgConcepto($event.target.value)" class="form-control"
                            style="text-transform:uppercase" onkeyup="javascript:this.value=this.value.toUpperCase();"
                            maxlength="100" placeholder="Concepto de movimiento...">
                    </div>
                </div>

                <!-- Bodega a Solicitar -->
                <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <h7 class="text-info">Bodega a Solicitar</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <select wire:model.defer="bodega" tabindex=3 id="bodega"
                            wire:change="asgBodega($event.target.value)" class="form-control text-center">
                            <option value="0">Elegir</option>
                            @foreach ($bodegas as $bd)
                                <option value="{{ $bd->id }}">{{ $bd->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Destino Producto -->
                <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <h7 class="text-info">Destino Producto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        @if ($modous == 1)
                            <select wire:model.defer="destino" tabindex=4 id="destino"
                                wire:change="asgDestino($event.target.value)" class="form-control text-center">
                                <option value="0">Elegir</option>
                                @foreach ($destinos as $d)
                                    <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                                @endforeach
                            </select>
                        @else
                            <select wire:model.defer="destino" tabindex=4 disabled id="destino"
                                wire:change="asgDestino($event.target.value)" class="form-control text-center">
                                <option value="0">Elegir</option>
                                @foreach ($destinos as $d)
                                    <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Fin 1a Fila -->

            @if ($bodega > 0 && $destino > 0)
                <!-- 2a Fila: Buscador de Producto, Stock, Presentación, Manejado Por, Cantidad, Botón Agregar -->
                <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false">
                    <!-- Buscador de Producto -->
                    <div class="form-group col-lg-4 col-md-12">
                        <h7 class="text-info">Buscador de Producto</h7>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="la la-search la-lg"></i>
                                </div>
                            </div>
                            <input tabindex=5 type="text" class="form-control" id="buscadorproducto"
                                placeholder="Buscar Producto..."
                                onkeyup="javascript:this.value=this.value.toUpperCase();" wire:model="lproducto"
                                @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                                @keydown.shift.tab="isOpen = false">
                        </div>
                        <ul class="list-group" x-show.transition.opacity="isOpen">
                            @if ($lproducto != '')
                                @foreach ($productos as $r)
                                    <li wire:click="seleProducto('{{ $r }}')"
                                        class="list-group-item list-group-item-action">
                                        <b>{{ $r->producto }}</b>
                                        - <h7 class="text-secondary">ID</h7>:{{ $r->id }}
                                        <h7 class="text-danger"> Stock</h7>:{{ $r->stock }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <!-- Stock -->
                    <div style="display: none;" id="bt1" class="form-group col-lg-1 col-md-3 col-sm-12">
                        <h7 class="text-info">Stock</h7>
                        <div class="input-group input-group-sm mb-2 mr-sm-2">
                            <input type="text" disabled wire:model.defer="elstock" id="stock"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Presentación -->
                    <div class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">
                        <h7 class="text-info" style="font-size: .70rem;">Presentación</h7>
                        <div class="input-group input-group-sm" style="font-size: .70rem;">
                            <div class="input-group-prepend" style="font-size: .70rem;">
                                <div class="input-group-text" style="font-size: .70rem;">
                                    <i class="la la-box la-lg"></i>
                                </div>
                            </div>
                            <input type="text" disabled wire:model.defer="lpresentacion" class="form-control"
                                style="font-size: .70rem;">
                        </div>
                    </div>

                    <!-- Manejado Por -->
                    <div class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">
                        <h7 class="text-info" style="font-size: .70rem;">Manejado Por</h7>
                        <div class="input-group input-group-sm" style="font-size: .70rem;">
                            <div class="input-group-prepend" style="font-size: .70rem;">
                                <div class="input-group-text" style="font-size: .70rem;">
                                    <i class="la la-user la-lg"></i>
                                </div>
                            </div>
                            <input type="text" disabled wire:model.defer="lmanejado" class="form-control"
                                style="font-size: .70rem;">
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div style="display: none;" id="bt2" class="form-group col-lg-2 col-md-3 col-sm-12">
                        <h7 class="text-info">Cantidad</h7>
                        <div class="input-group input-group-sm mb-2 mr-sm-2">
                            <input tabindex=6 type="text" wire:model.defer="lacantidad" id="cantidad"
                                wire:change="validaCantidad($event.target.value)"
                                wire:keydown.enter="validaCantidad($event.target.value)"
                                onkeypress="return CheckNumeric()" class="form-control">
                        </div>
                    </div>

                    <!-- Botón Agregar Producto -->
                    <div style="display: none;" id="bt3" class="form-group col-lg-1 col-md-3 col-sm-12 mt-4">
                        <button tabindex=7 class="btn btn-sm btn-dark" wire:click="saveLine()">
                            <i class="la la-edit la-lg"></i> Agregar
                        </button>
                    </div>
                </div>
                <!-- Fin 2a Fila -->

                <!-- Tabla Detalle -->
                <div class="table-responsive-sm mt-2">
                    <table id="tabladetalle" name="tabladetalle"
                        class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                        <thead>
                            <tr>
                                <th class="text-center col-2">ACCIONES</th>
                                <th class="text-center col-4">DESCRIPCIÓN</th>
                                <th class="text-center col-2">PRESENTACION</th>
                                <th class="text-center col-2">MANEJADO POR</th>
                                <th class="text-center col-1">CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody id="tbdetalle" name="tbdetalle">
                            @foreach ($detalle as $d)
                                <tr>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);"
                                                    onclick="Confirm('{{ $d['reg'] }}','{{ $d['producto'] }}')"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Quitar Movimiento">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="feather feather-trash-2 text-danger">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4
                                                                 a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10"
                                                            y2="17"></line>
                                                        <line x1="14" y1="11" x2="14"
                                                            y2="17"></line>
                                                    </svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-left">
                                        <b>{{ $d['productoid'] }}</b>-{{ $d['producto'] }}
                                    </td>
                                    <td class="text-center">
                                        {{ $d['presentacion'] }}
                                    </td>
                                    <td class="text-center">
                                        {{ $d['manejopor'] }}
                                    </td>
                                    <td class="text-center">{{ $d['cantidad'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Botones de Grabar o Descartar -->
                @if ($tregistros > 0)
                    <div id="botonGrabar" class="row justify-content-between">
                        <div class="col-lg-5 col-md-6 col-sm-12">
                            <button id="btgrabar" wire:click="grabarBd()" class="btn btn-block btn-primary mt-4">
                                Grabar Solicitud
                            </button>
                        </div>
                        <div class="col-lg-5 col-md-6 col-sm-12">
                            <button id="btgrabar" wire:click="descartarOperacion()"
                                class="btn btn-block btn-danger mt-4">
                                Descartar Solicitud
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@livewireScripts
<script type="text/javascript">
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;

    function preguntarAntesDeSalir() {
        if (bPreguntar) return "¿Seguro que quieres salir?";
    }
    //variables totales documentos

    function CheckNumeric() {
        return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
    }

    function muestraNumero(id) {
        swal({
            title: 'Confirmación',
            text: 'La Solicitud fue Grabada con el  No.: ' + id,
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar',
            closeOnConfirm: true
        })
    }

    function Confirm(id) {
        swal({
                title: 'CONFIRMAR',
                text: '¿DESEAS ELIMINAR EL REGISTRO?',
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
                toastr.success('', 'Registro eliminado con éxito')
                swal.close()
            })
    }

    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('confirmaOrden', idord => {
            muestraNumero(idord);
        })

        window.livewire.on('enable-bodega', accion => {
            if (accion == 1) {
                $('#bodega').removeAttr("disabled");
                $('#destino').removeAttr("disabled");
            } else {
                $('#bodega').attr("disabled", "disabled");
                $('#destino').attr("disabled", "disabled");
            }
        })

        window.livewire.on('enable-cajas', accion => {
            var div1 = document.getElementById('bt1');
            var div2 = document.getElementById('bt2');
            div1.style.display = 'block';
            div2.style.display = 'block';
            if (accion == 1) {
                $('#cantidad').focus();
            }
        })

        window.livewire.on('enable-grabalinea', accion => {
            var div3 = document.getElementById('bt3');
            div3.style.display = 'block';
        })
    })
</script>
