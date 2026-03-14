<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Cargos de *SERVICIOS* a Cuenta Paciente</b></h5>
                    </div> 
                </div>
            </div>
            <!--div datos head documento -->
            <div class="row">                     
                <div class="form-grou col-lg-4 col-md-3 col-sm-12">
                    <h7 class="text-info">Seleccionar Paciente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <select tabindex=1 wire:model.defer="pacienteid"  wire:change="asgDatosPaciente($event.target.value)" id="paciente" class="form-control text-center">
                            <option value="">Elegir</option>
                            @foreach($pacientes as $pac)
                                <option value="{{ $pac->id }}">
                                {{$pac->id}} >{{$pac->tipopaciente}} - {{$pac->nombrepaciente}}
                                </option>                                       
                            @endforeach                              
                        </select>	
                    </div>
				</div>
                <div class="form-group col-lg-3 col-md-12 col-sm-12">
                    <h7 class="text-info">REFERENTE</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="elreferente" id="referente" class="form-control">
                    </div>
				</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">HABITACION</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="elcuarto" id="cuarto" class="form-control">
                    </div>
               	</div>
           		<div class="form-group col-lg-3 col-md-4 col-sm-12">
               		<h7 class="text-info">FECHA Y HORA ADMISION</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="lafechahora" id="fechayhora" class="form-control">
                    </div>
               	</div>
            </div>
			<div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false"> 
                @if($pacienteid>0)                    
                <!--div buscador producto-->        
                <div class="form-group col-lg-4 col-md-12 " >
                    <h7 class="text-info">Buscador de Producto</h7>
                    <div class="input-group input-group-sm" >
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input tabindex=2 type="text"  class="form-control" id="buscadorproducto" placeholder="Buscar Producto..."
                        onkeyup="javascript:this.value=this.value.toUpperCase();"
                        wire:model="lproducto" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                        @keydown.shift.tab="isOpen = false">
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lproducto !='')
                        @foreach($productos as $r)
                        <li wire:click="asgDatosProducto('{{$r}}')" class="list-group-item list-group-item-action">                               
                            <b>{{$r->producto}}</b> - <h7 class="text-secondary">ID</h7>:{{$r->id}}
                            
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <!--fin buscador producto-->
                @endif

                <div style="display: none;"  id ="bt1" class="form-group col-lg-1 col-md-3 col-sm-12">
                    <h7  class="text-info">Cantidad</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input tabindex=3 type="text" wire:model.defer='lacantidad' 
                        id="cantidad" wire:change="validaCantidad($event.target.value)" wire:keydown.enter="validaCantidad($event.target.value)"
                        onkeypress="return CheckNumeric()" class="form-control">
                    </div>
				</div>

                <div style="display: none;"  id ="bt00" class="form-group col-lg-1 col-md-3 col-sm-12">
               		<h7 class="text-info">Precio Unit</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text"  tabindex=4 wire:model.defer='preciog'  id="preciog" class="form-control"
                        wire:change="validaPrecioU($event.target.value)" wire:keydown.enter="validaPrecioU($event.target.value)">
                    </div>
               	</div>

           		<div style="display: none;"  id ="bt2" class="form-group col-lg-1 col-md-3 col-sm-12">
               		<h7 class="text-info">Monto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled value='{{number_format($monto,2)}}'  id="monto" class="form-control">
                    </div>
               	</div>

                <div style="display: none;"  id ="bt3" class="col-2 mt-4">
                    <button tabindex=5 class="btn btn-sm btn-dark"  wire:click="saveLine()" >
                            <i class="la la-edit la-lg"></i>Agregar Producto
                    </button>
                </div>

            </div>
            <div class="table-responsive-sm mt-2">
                <table id="tabladetalle" name= "tabladetalle" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>                                                   
                            <th class="text-center col-2">ACCIONES</th>
                            <th class="text-center col-5">DESCRIPCIÓN</th>
                            <th class="text-center col-2">CANTIDAD</th>
                            <th class="text-center col-2">MTO VENTA</th>                        
                        </tr>
                    </thead>
                    <tbody id="tbdetalle" name="tdetalle">
                            @foreach($detalle as $d)
                                <tr>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);"          		
                                                wire:click="editarRegistro('{{$d['reg']}}')"
                                                data-toggle="tooltip" data-placement="top" title="Editar Registro">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                            </li>

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
                                    <td class="text-right">{{number_format($d['total'],2)}}</td>                                
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            @if($registros>0)
            <div  id="botonGrabar" class="row justify-content-between">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <button id="btgrabar" wire:click="grabarBd()"
                    class="btn  btn-block btn-primary mt-4">Grabar Operación</button>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <button id="btgrabar" wire:click="descartarOperacion()"
                    class="btn  btn-block btn-danger mt-4">Descartar Operación</button>
                </div>
            </div>
            @endif
		</div>
        @include('livewire.servicios.modal')    
	</div>
</div>

@livewireScripts
<script type="text/javascript">
    var cambioprecio='';
    var tasaiva =0;
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;
    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }


   
    function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function validaMonto()
    {
        cnt = Number($('#mdcantidad').val());
        pru = Number($('#mdpreciog').val());
        mto = Number((cnt*pru).toFixed(2))
        //iva = Number((gra * tasaiva).toFixed(4))
        //mto = Number((gra+iva).toFixed(2));
        //
        $('#mdmonto').val(mto);
 
    }

    function updLine()
    {
        cnt = Number($('#mdcantidad').val());
        pru = Number($('#mdpreciou').val());        
        if (cnt<0)
        {
            toastr.error("La cantidad debe ser mayor a 0")
            return
        }
        if (pru<0)
        {
            toastr.error("El Precio Unitario debe ser mayor a 0")
            return
        }
        
        $('#modalServicio').modal('hide');
        window.livewire.emit('updLine')    
    }


    function Confirm(id,prod)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL REGISTRO: '+prod+" ?",
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


        window.livewire.on('enable-cajas', accion=>
        {
            div = document.getElementById('bt1')
            div.style.display ='block'
            div = document.getElementById('bt2')
            div.style.display ='block'
            div = document.getElementById('bt3')
            div.style.display ='block'
            $('#cantidad').focus();
        })

        window.livewire.on('enable-unitario', accion=>
        {
            div = document.getElementById('bt1')
            div.style.display ='block'
            div = document.getElementById('bt2')
            div.style.display ='block'
            div = document.getElementById('bt3')
            div.style.display ='block'
            div = document.getElementById('bt00')
            div.style.display ='block'
        })
        window.livewire.on('disable-unitario', accion=>
        {
            div = document.getElementById('bt1')
            div.style.display ='block'
            div = document.getElementById('bt2')
            div.style.display ='block'
            div = document.getElementById('bt3')
            div.style.display ='block'
            div = document.getElementById('bt00')
            div.style.display ='none'
        })


        window.livewire.on('enable-grabar', accion=>
        {
            $('#paciente').attr("disabled", "disabled");
        })


        window.livewire.on('abrir-modal', accion=>
        {   
            cambioprecio=accion.cambio;
            tasaiva=accion.tasaiva
            if (cambioprecio=="SI")
            {
                $('#mdpreciou').removeAttr("disabled");
            }
            else
            {
                $('#mdpreciou').attr("disabled", "disabled");                
            }
            //
            $('#modalServicio').modal('show');
        })


   })
   
</script>