<div class="row layout-top-spacing">    
    <div class="col-xl-7 col-lg-7 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4" id="inventario">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5 style="font-size: .70rem;"><b>Entrada de Compra de Producto</b></h5>
                    </div> 
                </div>
            </div>
       		<!--div datos head documento -->
            <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false" id="inventarioDiv1">                     
                <div class="form-group col-lg-3 col-md-6 col-sm-12" style="font-size: .70rem;">
               		<h7 class="text-info" style="font-size: .70rem;">Fecha Ingreso</h7>
               		<div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                   		<div class="input-group-prepend" style="font-size: .70rem;"> 
                       		<div class="input-group-text" style="font-size: .70rem;">
								<i class="la la-calendar la-lg"></i>
							</div>
                   		</div>
                   		<input type="date" wire:model.defer="lafecha" id="lafecha" style="font-size: .70rem;" class="form-control" tabindex=1>
                   	</div>
               	</div>
                <!--div buscador Cliente-->        
                <div class="form-group col-lg-7 col-md-12 " style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Buscador de Proveedor</h7>
                    <div class="input-group input-group-sm mr-sm-2" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <a href="javascript:void(0);" wire:click=iniciarProveedor() data-toggle="tooltip" data-placement="top" title="Nuevo Proveedor" style="font-size: .70rem;">    
                                <i class="la la-file la-lg" ></i></a>
                            </div>
                        </div>
                        <input type="hidden" wire:model.lazy="sujeto_id" id="idprovee">
                        <input type="text" tabindex=2 class="form-control" placeholder="Buscar Proveedor..."
                            onkeyup="javascript:this.value=this.value.toUpperCase();" style="font-size: .70rem;"
                        wire:model="lsujeto" id="proveedor" @focus="isOpen = true" @keydown.escape.window="isOpen = false" 
                        @keydown.shift.tab="isOpen = false" @keydown.tab="isOpen = false">
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lsujeto !='')
                            @foreach($proveedores as $r)
                                <li wire:click="mostrarProveedor('{{$r->id}}')" class="list-group-item list-group-item-action">                               
                                    <b>Proveedor: {{$r->razon}}  </b> -<h7 class="text-success">NIT</h7>:{{$r->nit}} - 
                                    <h7 class="text-secondary">NRC</h7>:{{$r->nrc}}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <!--fin buscador Cliente-->
                <!--FUENTE PARA PAGO CONTADO-->
			</div>
            @if($sujetoid>0 || $sujetoid =='nuevo')
            <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false" style="font-size: .70rem;" id="inventarioDiv2">
                <div wire:ignore class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Bodega</h7>
                    <div class="input-group input-group-sm " style="font-size: .70rem;">
    					<select tabindex=2 wire:model.lazy="bodega"  id="bodega" onchange="asgBodega()" class="form-control text-center" style="font-size: .70rem;" tabindex=3>
 	    				<option value="" style="font-size: .70rem;">Elegir</option>
                        @foreach($bodegas as $bd)
                                <option value="{{ $bd->id }}" style="font-size: .70rem;">
                                    {{ $bd->nombre}}
                                </option>                                       
                        @endforeach                              
                        </select>	
                    </div>
				</div>
                @if($bodega>0)
                <!--div buscador producto-->        
                <div class="form-group col-lg-8 col-md-12 "  id="buscaPro1">
                    <h7 class="text-info" style="font-size: .70rem;">Buscador de Producto</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  tabindex=4 class="form-control" id="buscadorproducto"  placeholder="Buscar Producto..." style="font-size: .70rem;"
                        onkeyup="javascript:this.value=this.value.toUpperCase();" 
                        wire:model="lproducto" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                        @keydown.shift.tab="isOpen = false">
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lproducto !='')
                            @foreach($productos as $r)
                                <li wire:click="seleProducto('{{$r}}')" class="list-group-item list-group-item-action">                               
                                    <b>{{$r->producto}}</b> - <h7 class="text-secondary">ID</h7>:{{$r->id}}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                @endif
            </div>
            @if($productoid>0)
            <div class="row" >
                <!--fin buscador producto-->
                <div   id ="bt00" class="form-group col-lg-3 col-md-6 " style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Stock</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  disabled class="form-control"  value='{{$lstock}}' style="font-size: .70rem;" tabindex=5>
                    </div>
                </div>

                <div   id ="bt0" class="form-group col-lg-2 col-md-6 " style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">P.Venta(s/iva)</h7>
                    <div class="input-group input-group-sm" >
                        <input type="text"  disabled class="form-control"  value='{{number_format($lpreventa,4)}}' style="font-size: .70rem;" tabindex=6>
                    </div>
                </div>
                <div  id ="bt3" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">   
                    <h7 class="text-info" style="font-size: .70rem;">Cantidad</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input type="text"   id="cantidad" wire:model.defer="lcantidad" style="font-size: .70rem;"
                        onchange=valCantidad() onblur=valCantidad() tabindex=7
                        class="form-control" placeholder="cantidad">
                    </div>
                </div>
                <div   id ="bt4" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">   
                    <h7 class="text-info" style="font-size: .70rem;">Costo Uni</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input type="text"  wire:model.defer="lprecosto"  onchange=valCantidad() onblur=valCantidad() style="font-size: .70rem;" 
                        onkeypress="return CheckNumeric()" id="costounitario"  class="costo form-control" placeholder="costo" tabindex=8>
                    </div>
                </div>
                <div  id ="bt5" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">   
                    <h7 class="text-info" style="font-size: .70rem;">Total</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input type="text" id="montoproducto"  value='{{number_format($lmonto,4)}}' class="costo form-control" style="font-size: .70rem;"
                        disabled placeholder="total" tabindex=9>
                    </div>
                </div>
            </div>
            <div class="row" id="inventarioDiv4">
                @if($lotesprodsum >= 1)
                <div  id ="bt1" class="form-grou col-lg-3 col-md-6 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Lote Vence</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <select  wire:model.defer="loteid" id="lote"  wire:change="asgLote($event.target.value)" class="form-control text-center" style="font-size: .70rem;" tabindex=10>
                            @if($lotesprodsum>1)
                                <option value="" style="font-size: .70rem;">Seleccionar Lote</option>
                                <option value="" style="font-size: .70rem;">Sin Lote</option>                                
                            @endif
                            @if($lotesprodsum==1)
                                <option value="" style="font-size: .70rem;">Sin Lote</option>                                
                            @endif
                            @foreach($lotevence as $l)
                                    <option value="{{ $l->id }}" style="font-size: .70rem;">
                                        {{ $l->mes ."/". $l->anno}} -> {{ $l->total }}
                                    </option>                                       
                            @endforeach                              

                        </select>	
                    </div>
                </div>
                @endif
                <div   id ="bt2" class="form-group col-lg-3 col-md-6 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;"><b>Nuevo Lote</b></h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <input type="text" tabindex=11 wire:model.defer="newlote" wire:change="validaNuevoLote($event.target.value)" style="font-size: .70rem;"
                        wire:keydown.enter="validaNuevoLote($event.target.value)" id="lotenew"
                        class="lotevence form-control" placeholder="00/0000">
                    </div>
                </div>
                @if($lmonto > 0)
                <div  class="form-group col-lg-3 col-md-3 col-sm-12 mt-3" style="font-size: .70rem;">
                    <button tabindex=8 wire:click="saveLine()"  type="button" acceskey="g" class="btn btn-sm btn-dark" style="font-size: .70rem;" tabindex=12>
                    Agregar(g) 
                    </button>
                </div>
                @endif
            </div>
            @endif
            @endif
            @if($detalle->count() > 0)
            <div class="row" id="inventarioDiv5">
            <div class="form-group col-lg-12 col-md-3 col-sm-12 mt-3">
                <div class="table-responsive-sm mt-2" style="font-size: .70rem;">
                    <table id="tabladetalle" name= "tabladetalle" style="font-size: .70rem;" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head">
                        <thead>
                            <tr>                                                   
                                <th class="text-center col-1" style="font-size: .70rem;">ACCIONES</th>
                                <th class="text-center col-2" style="font-size: .70rem;">BODEGA</th>
                                <th class="text-center col-4" style="font-size: .70rem;">ID - NOMBRE PRODUCTO</th>
                                <th class="text-center col-1" style="font-size: .70rem;">CNT</th>
                                <th class="text-center col-1" style="font-size: .70rem;">Ct. UNIT</th>
                                <th class="text-center col-1" style="font-size: .70rem;">SUBTOTAL</th>                        
                                <th class="text-center col-1" style="font-size: .70rem;">IVA</th>                        
                                <th class="text-center col-1" style="font-size: .70rem;">MONTO</th>
                                <th class="text-center col-2" style="font-size: .70rem;">VECTO</th>
                            </tr>
                        </thead>
                        <tbody id="tbdetalle" name="tbdetalle" style="font-size: .70rem;">
                            @foreach($detalle as $d)
                                <tr>
                                    <td class="text-center" style="font-size: .70rem;">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);" style="font-size: .70rem;"
                                                onclick="Confirm('{{$d['reg']}}','{{$d['producto']}}')"
                                                data-toggle="tooltip" data-placement="top" title="Quitar Movimiento">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-left" style="font-size: .65rem;">{{$d['cbodega']}}</td>
                                    <td class="text-left" style="font-size: .65rem;"><b>{{$d['productoid']}}</b>-{{$d['producto']}}</td>
                                    <td class="text-center" style="font-size: .65rem;">{{$d['cantidad']}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['pcostou'],4)}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['subtotal'],4)}}</td>                            
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['iva'],4)}}</td>                            
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['total'],2)}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{$d['cvence']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center " style="font-size: .70rem;"></th>
                                <th class="text-center " style="font-size: .70rem;"></th>                        
                                <th class="text-center " style="font-size: .70rem;"></th>
                                <th class="text-center " style="font-size: .70rem;"></th>                        
                                <th class="text-center " style="font-size: .70rem;">TOTAL:</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('subtotal'),4)}}</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('iva'),4)}}</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('total'),2)}}</th>                        
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if($detalle->sum('total')>0)
                    @if ($etapa==0)
                        <div id="botonGrabar" class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabari" wire:loading.attr="disabled" wire:target="grabaMovInventario" wire:click.prevent="grabaMovInventario()"
                                class="btn btn-sm btn-block btn-primary mt-4">Grabar Mv.Inventario</button>
                            </div>

<!--                             <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btfiscal" wire:click="iniciaFiscal()"
                                class="btn btn-sm btn-block btn-success mt-4">>>Info Fiscal</button>
                            </div>
 -->                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btdescarar" wire:click="descartaInventario()"
                                class="btn btn-sm btn-block btn-warning mt-4">Descartar Operación</button>
                            </div>
                        </div>
                    @else
                        <div id="botonGrabar" class="row">                    
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabari" disabled class="btn btn-sm btn-block btn-primary mt-4">Grabar Mv.Inventario</button>
                            </div>
<!--                             <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabar"  disabled class="btn btn-sm btn-block btn-success mt-4">Info Fiscal</button>
                            </div>
 -->                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btdescarar" disabled class="btn btn-sm btn-block btn-warning mt-4">Descartar Operación</button>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            </div>            
            @endif

		</div>
	</div>
    @include('livewire.compras.modalproveedor')            
    @include('livewire.compras.modalserie')    
</div>

@livewireScripts
<script type="text/javascript">
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;
    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }
    datoslote=[];
    conlote=0;
    datosproducto=[];
    regs=0;
    registro=[];
    //variables totales documentos


   function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function nuevaSerie()
    {
        $('#lserie').val('')
        $('#modalserie').modal('show')
        $('#lserie').focus()
    }

    function nuevoProveedor()
    {
        validaFecha();
        //$('#mrazon').val('')
        //$('#mnit').val('')
        //$('#mnrc').val('')
        //$('#mduit').val('')
        //$('#mgiro').val('')

        $('#modalproveedor').modal('show')
        $('#mrazon').focus()
    }

    function saveRegPda()
    {
        $('#editaregistromodal').modal('hide')
        var cta = $('#selectcuenta').val()
        window.livewire.emit('grabarRegistro',cta);
    }

    function grabarenIva()
    {
        $('#modalgrabaiva').modal('show')
    }

    function asgBodega()
    {


        var vv=$('#bodega').val()
        $('.lotevence').mask('00/0000');
        window.livewire.emit('asgBodega', vv);
    }


    function saveProveedor()
    {
        if ($('#mrazon').val()=='')
        {
            toastr.error('Debe indicar un nombre de Proveedor...');
            return
        }
        if ($('#mnit').val()=='')
        {
            toastr.error('Debe indicar un Nùmero de Nit...')
            return
        }
         window.livewire.emit('grabaProveedor');
         //,$('mrazon').val(),$('mdui').val(),$('mnit').val(),$('mnrc').val(),$('mgiro').val()
         $('#modalproveedor').modal('hide')
    }    


    function saveSerie()
    {
        if ($('lserie').val('') != '')
        {
         window.livewire.emit('saveSerie',$('lserie').val());
         $('#modalserie').modal('hide')

        }
        else
        {
            toastr.error('No se puede grabar una serie en Blanco...');
        }
    }

   function Confirm(id,prod)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL REGISTRO: '+prod+'?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
        },
        function()
        {

            window.livewire.emit('deleteRow', id)    

            toastr.success('', 'Registro eliminado con éxito')

            swal.close()
        })
    }

   function valCantidad()
    {
        vv=Number($('#cantidad').val())
        v1=Number($('#costounitario').val())
        $('.lotevence').mask('00/0000');
        window.livewire.emit('validaCantidad', vv,v1)    
    }

   function muestraNumero(id)
    {
       swal(
        {
        title: 'Confirmación',
        text: 'La Compra fue Grabado con el movimiento No.: '+id,
        type: 'success',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar',
        closeOnConfirm: true
        })
   }

    function roundTo2(num)
    {
        var valor = num.value;
       num.value =  parseFloat(Number(valor)).toFixed(2);
    }

    function saveProveedor()
    {
        if($('#mrazon').val() == '')
        {
           toastr.error('Debe indicar un nombre de Proveedor....')
           return;
        }
        if($('#mnit').val() == '' && $('#mdui').val() == '')
        {
           toastr.error('Debe indicar un NIT ó DUI del Proveedor....')
           return;
        }
        window.livewire.emit('grabaProveedor')
        $('#modalproveedor').modal('hide')
    }

    function calcula()
    {
        window.livewire.emit('calculaT');
    }

    function agregaRegistro()
    {
        window.livewire.emit('agregaRegistro');
    }


    function grabaOperacion()
    {
        window.livewire.emit('grabaProceso');
    }

    function validaCargo()
    {
        window.livewire.emit('validaCargo',Number($('#rgcargo').val()));
    }
    function validaAbono()
    {
        window.livewire.emit('validaAbono',Number($('#rgabono').val()));
    }
    function editaRegistro(id)
    {
        window.livewire.emit('editaRegistro',id)
    }

    function descartaP()
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿Seguro de Descartar EL PROCESO CONTABLE ACTUAL ????',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
        },
        function()
        {
        window.livewire.emit('descartarOperacion');   
        swal.close()
        })
    }

    function mostrarDatos()
    {
    datosproducto = document.getElementById('selectcuenta').value.split('_');

    $('#selectcuenta').val(datosproducto[0]);

    }
    function saveLine()
    {
        $('#editaregistromodal').modal('hide')
        window.livewire.emit('grabarRegistro',$('#selectcuenta').val());
    }


    document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('print-mov', idMov => {
        var wactual = window;
        var ruta = "{{url('detallemov')}}" + '/' + idMov;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })

        window.livewire.on('confirmaOrden',idord=>
        {
            muestraNumero(idord);
        })

        window.livewire.on('iniciaEntorno', nuevoP =>{
            $('.lotevence').mask('00/0000');
            $('.money').mask('000,000,000.0000', {reverse: true});
  	    	$('.movil').mask('0000-0000');
      		$('.dui').mask('00000000-0');
  		    $('.nit').mask('0000-000000-000-0');		 
        })

        window.livewire.on('loteVenceMask', nuevoP =>{
            $('.lotevence').mask('00/0000');
        })

        window.livewire.on('nuevoProveedor', nuevoP =>{
            $('#modalproveedor').modal('show')
            $('.money').mask('000,000,000.0000', {reverse: true});
  	    	$('.movil').mask('0000-0000');
      		$('.dui').mask('00000000-0');
  		    $('.nit').mask('0000-000000-000-0');		 
            $('#mrazon').focus();
        })

        window.livewire.on('abreModalRg', idMov =>
        {
            $('#editaregistromodal').modal('show')
            $('#selectcuenta').select2({
             dropdownParent: $('#editaregistromodal')
            });
            //$('#selectcuenta').val(idMov)
            $('#selectcuenta').focus()

        })

        window.livewire.on('noInventarioDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("inventario").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("inventario").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })

        window.livewire.on('noFiscalDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("fiscal").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("fiscal").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })

        window.livewire.on('noContableDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("contable").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("contable").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })


   })
 
</script>