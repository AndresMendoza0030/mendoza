<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Ajuste Existencia de Productos</b></h5>
                    </div>
                </div>
            </div>

            <!--div datos head documento -->
            <div class="row mt-1">
                <div class="form-group col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Fecha</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-calendar la-lg"></i>
                            </div>
                        </div>
                        <input tabindex=1 type="date" wire:model.defer="lafecha" id="lafecha" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-5 col-md-12 col-sm-12">
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
                <div class="form-grou col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Tipo Movimiento</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <select tabindex=3 wire:model.defer="tipomov" id="tipomov" class="form-control text-center">
                            <option value="0">Elegir</option>
                            <option value="1">Entrada</option>
                            <option value="2">Salida</option>
                        </select>
                    </div>
                </div>
                <div class="form-grou col-lg-3 col-md-4 col-sm-12">
                    <h7 class="text-info">Bodega Operación</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <select tabindex=4 wire:model.defer="bodega" id="bodegas"
                            wire:change="asgBodega($event.target.value)" class="form-control text-center">
                            <option value="0">Elegir</option>
                            @foreach ($bodegas as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @if ($bodega > 0)
                <div class="row">
                    <!--div buscador producto-->
                    <div class="form-group col-lg-5 col-md-12 ">
                        <h7 class="text-info">Buscador de Producto</h7>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="la la-search la-lg"></i>
                                </div>
                            </div>
                            <input style="font-size: .70rem;" type="text" tabindex=5 class="form-control"
                                id="buscadorproducto" placeholder="Buscar Producto..."
                                onkeyup="javascript:this.value=this.value.toUpperCase();" wire:model="lproducto"
                                @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                                @keydown.enter.window="isOpen = false" @keydown.shift.tab="isOpen = false">
                        </div>
                        <ul class="list-group" x-show.transition.opacity="isOpen">
                            @if ($lproducto != '')
                                @foreach ($productos as $r)
                                    @if ($tipomov == 2)
                                        @if ($r->stock > 0)
                                            <li wire:click="seleProducto({{ $r->id }})"
                                                class="list-group-item list-group-item-action">
                                                <b style="font-size: .70rem;">{{ $r->producto }}</b> - <h7
                                                    style="font-size: .70rem;" class="text-secondary">ID</h7>
                                                :{{ $r->id }}
                                                <h7 style="font-size: .70rem;" class="text-danger"> Stock</h7>
                                                :{{ $r->stock }}
                                            </li>
                                        @endif
                                    @endif
                                    @if ($tipomov == 1)
                                        <li wire:click="seleProducto({{ $r->id }})"
                                            class="list-group-item list-group-item-action">
                                            <b style="font-size: .70rem;">{{ $r->producto }}</b> - <h7
                                                style="font-size: .70rem;" class="text-secondary">ID</h7>
                                            :{{ $r->id }}
                                            <h7 style="font-size: .70rem;" class="text-danger"> Stock</h7>
                                            :{{ $r->stock }}
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <!--fin buscador producto-->

                    <div style="display: none;" id ="bt1" class="form-group col-lg-1 col-md-4 col-sm-12">
                        <h7 class="text-info">Stock</h7>
                        <div class="input-group input-group-sm">
                            <input style="font-size: .70rem;" type="text" wire:model.defer="elstock" id="stock"
                                disabled class="form-control" placeholder="Stock">
                        </div>
                    </div>
                    <div style="display: none;" id ="bt2" class="form-grou col-lg-2 col-md-12 col-sm-12">
                        <h7 class="text-info">Lote Vence</h7>
                        <div class="input-group input-group-sm mb-2 mr-sm-2">
                            <select style="font-size: .70rem;" tabindex=6 wire:model.defer="loteid" id="lote"
                                wire:change="asgLote($event.target.value)" class="form-control text-center">
                                @if ($lotesprodsum > 1)
                                    <option value="">Seleccionar Lote</option>
                                @endif
                                @if ($lotesprodsum == 1)
                                    <option value="">Sin Lote</option>
                                @endif
                                @foreach ($lotevence as $l)
                                    <option value="{{ $l->id }}">
                                        {{ $l->mes . '/' . $l->anno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: none;" id ="bt3" class="form-grou col-lg-2 col-md-12 col-sm-12">
                        <h7 class="text-info"><b>Nuevo Lote</b></h7>
                        <div class="input-group input-group-sm mb-2 mr-sm-2">
                            <input type="text" tabindex=7 style="font-size: .70rem;" wire:model.defer="newlote"
                                wire:change="validaNuevoLote($event.target.value)"
                                wire:keydown.enter="validaNuevoLote($event.target.value)" id="lotenew"
                                class="lotevence form-control" placeholder="00/0000">
                        </div>
                    </div>

                    <div style="display: none;" id ="bt4" class="form-group col-lg-1 col-md-4 col-sm-12">
                        <h7 class="text-info">Cantidad</h7>
                        <div class="input-group input-group-sm">
                            <input tabindex=8 style="font-size: .70rem;" type="text" wire:model.defer="lacantidad"
                                id="cantidad" placeholder="cantidad"
                                wire:change="validaCantidad($event.target.value)"
                                wire:keydown.enter="validaCantidad($event.target.value)"
                                onkeypress="return CheckNumeric()" class="form-control">
                        </div>
                    </div>

                    <div style="display: none;" id ="bt5" class="form-group col-lg-2 col-md-3 col-sm-12  mt-4">
                        <button tabindex=9 class="btn btn-sm btn-dark" wire:click="saveLine()">
                            <i class="la la-edit la-lg"></i>Agregar Producto
                        </button>
                    </div>

                </div>

                <div>
                    <table id="tabladetalle" name= "tabladetalle"
                        class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                        <thead>
                            <tr>
                                <th class="text-center col-2">ACCIONES</th>
                                <th class="text-center col-5">DESCRIPCIÓN</th>
                                <th class="text-center col-2">CANTIDAD</th>
                                <th class="text-center col-3">VECTO</th>

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
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                        </path>
                                                        <line x1="10" y1="11" x2="10"
                                                            y2="17"></line>
                                                        <line x1="14" y1="11" x2="14"
                                                            y2="17"></line>
                                                    </svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-left"><b>{{ $d['productoid'] }}</b>-{{ $d['producto'] }}</td>
                                    <td class="text-center">{{ $d['cantidad'] }}</td>
                                    <td class="text-right">{{ $d['cvence'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($tregistros > 0)
                    <div class="row justify-content-between">
                        <div class="col-lg-5 col-md-6 col-sm-12">
                            <button id="btgrabar" wire:click="grabarBd()"
                                class="btn  btn-block btn-primary mt-4">Grabar Operación</button>
                        </div>
                        <div class="col-lg-5 col-md-6 col-sm-12">
                            <button id="btgrabar" wire:click="descartarOperacion()"
                                class="btn  btn-block btn-danger mt-4">Descartar Operación</button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@livewireScripts
<script type="text/javascript">
    var conlote = 0;
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;

    function preguntarAntesDeSalir() {
        if (bPreguntar)
            return "¿Seguro que quieres salir?";
    }

    regs = 0;
    registro = [];
    lotesEx = [];
    datosproducto = [];
    datoslote = [];
    accion = 1;
    editno = 0;
    //variables totales documentos

    function CheckNumeric() {
        return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
    }


    function muestraNumero(id) {
        swal({
            title: 'Confirmación',
            text: 'El Ajuste fue Grabado con el movimiento No.: ' + id,
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
        window.livewire.on('print-mov', idMov => {
            var wactual = window;
            var ruta = "{{ url('detallemov') }}" + '/' + idMov;
            var w = window.open(ruta, "_blank");
            wactual.focus();
        })

        window.livewire.on('confirmaOrden', idord => {
            muestraNumero(idord);
        })

        window.livewire.on('abrecaja', accion => {
            $('.lotevence').mask('00/0000')

            div = document.getElementById('bt1')
            div.style.display = 'block'

            if (accion.nlotes >= 1) {
                div = document.getElementById('bt2')
                div.style.display = 'block'
            } else {
                div = document.getElementById('bt2')
                div.style.display = 'none'
            }

            if (accion.tipomov == 1) {
                div = document.getElementById('bt3')
                div.style.display = 'block'
            }

            div = document.getElementById('bt4')
            div.style.display = 'block'


            //
            $('#cantidad').focus();
        })

        window.livewire.on('enable-cajas', accion => {
            $('.lotevence').mask('00/0000');

            div = document.getElementById('bt1');
            div.style.display = 'block';

            if (accion.nlotes >= 1) {
                div = document.getElementById('bt2');
                div.style.display = 'block';
            } else {
                div = document.getElementById('bt2');
                div.style.display = 'none';
            }

            if (accion.tipomov == 1) {
                div = document.getElementById('bt3')
                div.style.display = 'block'
            }


            div = document.getElementById('bt4')
            div.style.display = 'block';
            //

            if (accion.grabar == "S") {
                div = document.getElementById('bt5')
                div.style.display = 'block'
            }

            if (accion.registros >= 1) {
                $('#bodegas').attr("disabled", "disabled");
                $('#tipomov').attr("disabled", "disabled");
            }

            if (accion.muestrac == 1) {
                $('#cantidad').focus();
            }
        })

        window.livewire.on('enable-grabar', accion => {
            div = document.getElementById('bt5')
            div.style.display = 'block'

            //
            div = document.getElementById('bt1')
            div.style.display = 'block';

            if (accion.nlotes >= 1) {
                div = document.getElementById('bt2');
                div.style.display = 'block';
            } else {
                div = document.getElementById('bt2');
                div.style.display = 'none';
            }

            if (accion.tipomov == 1) {
                div = document.getElementById('bt3')
                div.style.display = 'block'
            }

            div = document.getElementById('bt4')
            div.style.display = 'block';


            if (accion.registros >= 1) {
                $('#bodegas').attr("disabled", "disabled");
                $('#tipomov').attr("disabled", "disabled");
            }
        })


        window.livewire.on('enable-bodega', accion => {
            if (accion == 1) {
                $('#bodegas').attr("disabled", "disabled");
                $('#tipomov').attr("disabled", "disabled");
            } else {
                $('#tipomov').removeAttr("disabled");
                $('#bodegas').removeAttr("disabled");
            }

        })



    })
</script>
