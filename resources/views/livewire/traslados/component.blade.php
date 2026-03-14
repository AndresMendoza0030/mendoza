<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Traslados de Producto</b></h5>
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
                   		<input type="date" wire:model.defer="lafecha" class="form-control">
                   	</div>
               	</div>
                <div class="form-group col-lg-6 col-md-12 col-sm-12">
                    <h7 class="text-info">Concepto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                    	<div class="input-group-prepend">
                       		<div class="input-group-text">
								<i class="la la-edit la-lg"></i>
							</div>
                    	</div>
                    	<input type="text" wire:model.defer="concepto" wire:change="asgConcepto($event.target.value)" class="form-control"
                        style="text-transform:uppercase" onkeyup="javascript:this.value=this.value.toUpperCase();"
    					maxlength="100" placeholder="Concepto de movimiento...">
                    </div>
				</div>

                <div wire:ignore class="form-grou col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Bodega Origen</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.lazy="bodega"  id="bodega" wire:change="asgBodega($event.target.value)" class="form-control text-center">
 						<option value="0">Elegir</option>
 	    				@foreach($bodegas as $bd)
 							<option value="{{ $bd->id }}" >
 								{{ $bd->nombre}}
 							</option>                                       
						@endforeach                              
 					</select>	
                    </div>
				</div>
                <div wire:ignore class="form-grou col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Bodega Destino</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.lazy="bodega2" wire:change="asgBodega2($event.target.value)" id="bodega2" class="form-control text-center">
 						<option value="0">Elegir</option>
 	    				@foreach($bodegas as $bd2)
 							<option value="{{ $bd2->id }}" >
 								{{ $bd2->nombre}}
 							</option>                                       
						@endforeach                              
 					</select>	
                    </div>
				</div>
            </div>
            @if($bodega>0 && $bodega2>0)
            <div class="row">                     
                <!--div buscador producto-->        
                <div class="form-group col-lg-4 col-md-12 " >
                    <h7 class="text-info">Buscador de Producto</h7>
                    <div class="input-group input-group-sm" >
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  class="form-control" id="buscadorproducto" placeholder="Buscar Producto..."
                        onkeyup="javascript:this.value=this.value.toUpperCase();"
                        wire:model="lproducto" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                        @keydown.shift.tab="isOpen = false">
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lproducto !='')
                        @foreach($productos as $r)
                        <li wire:click="seleProducto('{{$r}}')" class="list-group-item list-group-item-action">                               
                            <b>{{$r->producto}}</b> - <h7 class="text-secondary">ID</h7>:{{$r->id}}
                            <h7 class="text-danger">  Stock</h7>:{{$r->stock}}                            
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <!--fin buscador producto-->

                <div style="display: none;" id ="bt1" class="form-group col-lg-1 col-md-4 col-sm-12">   
                    <h7 class="text-info">Stock</h7>
                    <div class="input-group input-group-sm">
                        <input type="text"  wire:model.defer="elstock" 
                        disabled class="form-control" placeholder="Stock">
                    </div>
                </div>

                <div style="display: none;"  id ="bt2" class="form-grou col-lg-2 col-md-12 col-sm-12">
                    <h7 class="text-info">Lote Vence</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <select  tabindex=6 wire:model.defer="loteid" id="lote"  class="form-control text-center">
                            @if($lotesprodsum>1)
                                <option value="">Seleccionar Lote</option>
                            @endif
                            @if($lotesprodsum==1)
                                <option value="">Sin Lote</option>                                
                            @endif
                            @foreach($lotevence as $l)
                                    <option value="{{ $l->id }}" >
                                        {{ $l->mes ."/". $l->anno}}
                                    </option>                                       
                            @endforeach                              
                        </select>	
                    </div>
				</div>

                <div style="display: none;" id ="bt3" class="form-group col-lg-2 col-md-4 col-sm-12">   
                    <h7 class="text-info">Cantidad Salida</h7>
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model.defer="lacantidad" placeholder="cantidad"
                        wire:change="validaCantidad($event.target.value)" wire:keydown.enter="validaCantidad($event.target.value)"
                        onkeypress="return CheckNumeric()" class="form-control">
                    </div>
                </div>
                <div style="display: none;"  id ="bt4" class="form-group col-lg-2 col-md-3 col-sm-12  mt-4">
                    <button tabindex=6 class="btn btn-sm btn-dark"  wire:click="saveLine()" >
                            <i class="la la-edit la-lg"></i>Agregar Producto
                    </button>
                </div>
            </div>
            <div  class="table-responsive-sm mt-2">
                <table id="tabladetalle" name= "tabladetalle" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>                                                   
                            <th class="text-center col-2">ACCIONES</th>
                            <th class="text-center col-5">DESCRIPCIÓN</th>
                            <th class="text-center col-2">CANTIDAD</th>
                            <th class="text-center col-3">VECTO</th>
                        </tr>
                    </thead>
                    <tbody id="tbdetalle" name="tbdetalle">
                        @foreach($detalle as $d)
                            <tr>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm('{{$d['reg']}}','{{$d['producto']}}')"
                                            data-toggle="tooltip" data-placement="top" title="Quitar Movimiento">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="text-left"><b>{{$d['productoid']}}</b>-{{$d['producto']}}</td>
                                <td class="text-center">{{$d['cantidad']}}</td>
                                <td class="text-right">{{$d['cvence']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tregistros>0)
                <div  class="row justify-content-between">
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
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;
    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }

    conlote =0;
    datoslote=[];
    datosproducto=[];
    registro=[];
    regs=0;
    //variables totales documentos



   function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}


   function muestraNumero(id)
    {
       swal(
        {
        title: 'Confirmación',
        text: 'El Traslado fue Grabado con el movimiento No.: '+id,
        type: 'success',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar',
        closeOnConfirm: true
        })
   }

   function Confirm(id)
    {
       swal(
        {
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
        function()
        {
            window.livewire.emit('deleteRow', id)    
            toastr.success('', 'Registro eliminado con éxito')
            swal.close()
        })
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

        window.livewire.on('enable-cajas', accion=>
        {
            div = document.getElementById('bt1')
            div.style.display ='block'

            if (accion.nlotes>=1)
            {
                div = document.getElementById('bt2')
                div.style.display ='block'
            }
            else
            {
                div = document.getElementById('bt2')
                div.style.display ='none'
            }
            div = document.getElementById('bt3')
            div.style.display ='block'

            if (accion.muestrac==1)
            {
                $('#cantidad').focus();
            }
        })
        window.livewire.on('enable-grabar', accion=>
        {
            div = document.getElementById('bt4')
            div.style.display ='block'

            if (accion>=1)
            {
                $('#bodega').attr("disabled", "disabled");
                $('#bodega2').attr("disabled", "disabled");
            }
        })

        window.livewire.on('enable-bodega', accion=>
        {
            if (accion==1)
            {
                $('#bodega').attr("disabled", "disabled");
                $('#bodega2').attr("disabled", "disabled");
            }
            else
            {
                $('#bodega').removeAttr("disabled");
                $('#bodega2').removeAttr("disabled");
            }

        })

   })
   
</script>