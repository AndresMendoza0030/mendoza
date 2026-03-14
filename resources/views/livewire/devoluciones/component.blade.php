<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing" x-data="{ isOpen: true }" @click.away="isOpen = false">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Devolución Producto/Servicio Cta Paciente</b></h5>
                    </div> 
                </div>
            </div>
            <div class="row mt-3">
                <div class="form-grou col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Seleccionar Paciente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.defer="pacienteid"  wire:change="asgDatosPaciente($event.target.value)" id="paciente" class="form-control text-center">
 						<option value="">Elegir</option>
 	    				@foreach($pacientes as $pac)
 							<option value="{{ $pac->id }}">
                             {{$pac->id}} >{{$pac->tipopaciente}} - {{$pac->nombrepaciente}}
 							</option>                                       
						@endforeach                              
 					</select>	
                    </div>
				</div>

                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Md.Referente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="elmedico" id="elmedico" class="form-control">
                    </div>
				</div>
           		<div class="form-group col-lg-1 col-md-4 col-sm-12">
               		<h7 class="text-info">Cuarto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="elcuarto" id="elcuarto" class="form-control">
                    </div>
               	</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">FECHA Y HORA ADMISION</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="lafechayhora" id="lafechayhora" class="form-control">
                    </div>
               	</div>
            </div>      
            <!--div datos head documento -->
            @if($pacienteid>0)
            <div class="row">                     
                @if(count($vales) > 0)
                <div class="form-grou col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Vales de Cargo por Paciente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.defer="elvale" id="elvale" wire:change="asgElVale($event.target.value)" class="form-control text-center">
						<option value="">Elegir</option>
 	    				@foreach($vales as $vl)
 							<option value="{{ $vl->documento }}">
 						    {{ $vl->infovale}}
 							</option>                                       
						@endforeach 
 					</select>	
                    </div>
				</div>
                @endif

                <div class="form-group col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Documento No.</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="elvale"  class="form-control">
                    </div>
				</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">Fecha Doc.</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="fechadoc"  class="form-control">
                    </div>
               	</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">Bodega Operación</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.defer="labodega"  class="form-control">
                    </div>
               	</div>
			</div>
            <div class="row">                     
                <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
                    <div class="card text-center">
                        <h7 class="card-header"><b>Detalle del Vale de Cargo del Paciente</b></h7>
                    </div>
                    <div class="table-responsive-sm ">
                        <table id="tbcargo" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                            <thead>
                                <tr>                                                   
                                    <th class="text-center col-2">ACCIONES</th>
                                    <th class="text-center col-4">PRODUCTO/SERVICIO</th>
                                    <th class="text-center col-2">CNT CARGADO</th>
                                    <th class="text-center col-2">LOTE VCTO</th>
                                    <th class="text-center col-2">A DEVOLVER</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($info) > 0)
                            @foreach($info as $r)
                                <tr>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            @if($r['cantidad']!=$r['yadevuelto'])
                                            <li>
                                                <a href="javascript:void(0);" wire:click="editarDevolver('{{$r['reg']}}')"
		                                        data-toggle="tooltip" data-placement="top" title="Indicar Cantidad a Devolver">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="brown" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg>                                                
                                                </a>
	                                        </li>
                                            <li>
                                                <a href="javascript:void(0);" wire:click="devuelveTodo('{{$r['reg']}}')"         		
		                                        data-toggle="tooltip" data-placement="top" title="Devolución Total">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>                                                
                                                </a>
                                            <li>
                                            <li>
                                                <a href="javascript:void(0);" wire:click="desHaceDevolucion('{{$r['reg']}}')"
		                                        data-toggle="tooltip" data-placement="top" title="Deshacer Cantidad a Devolver">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
	                                        </li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="text-left">{{$r['producto']}}</p></td>
                                    <td class="text-center">{{$r['cantidad']}}</td>

                                    @if($r['lotevence'] != 0)
                                        <td class="text-right">{{$r['mes']}}/{{$r['anno']}}</td>
                                    @endif
                                    @if($r['lotevence'] == 0)
                                    <td> </td>
                                    @endif
                                    <td class="text-center">{{$r['devuelto']}}</td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                        @if($elvale > 0)
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <button id="btaddall" wire:click="devuelveTodoVale()"class="btn btn-sm btn-block btn-success"
                            data-toggle="tooltip" data-placement="top" title="Procesa Todo el Vale como Devolución...">
                            DEVUELVE TODO EL VALE</button>
                        </div>
                        @endif
                    </div>
                </div>
		    </div>
            @endif
        </div>
        @if($condevo==1)
        <div id="botonGrabar" class="row justify-content-between">
    		<div class="col-lg-6 col-md-6 col-sm-12">
               	<button id="btgrabar" wire:click="grabarOperacion()"
		 		class="btn  btn-block btn-primary mt-4"
                data-toggle="tooltip" data-placement="top" title="Grabar Devolución en Cuenta de Paciente...">
                Grabar Devolución</button>
            </div>
    		<div class="col-lg-6 col-md-6 col-sm-12">
               	<button id="btgrabar" wire:click="descartarOperacion()"
		 		class="btn  btn-block btn-danger mt-4"
                data-toggle="tooltip" data-placement="top" title="Descartar Toda la Operación...">>
                Descartar Operación</button>
            </div>
        </div>
        @endif

        @include('livewire.devoluciones.modal')    
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

    datosproducto=[];
    datospaciente=[];
    regs=0;
    registro=[];
    var detalle=[];
    accion=1;
    editno=0;
    //variables totales documentos


   function validarDevolver()
   {
        if ( Number($('#mddevuelto').val()) >= Number($('#cdevolver').val())) 
        {
            toastr.error('La cantidad a devolver no puede ser mayor que la salida...');
            return;
        }

    }


   function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}





   document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('msg-ok', dataMsg =>{ $('#modalCompraProd').modal('hide')})

        window.livewire.on('print-mov', idMov => {
        var wactual = window;
        var ruta = "{{url('detallemov')}}" + '/' + idMov;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })

        window.livewire.on('abrir-modal', idMov =>
        {
            $('#modalLine').modal('show');
            $('#cdevolver').focus();
        })

        
        window.livewire.on('cerrar-modal', idMov =>
        {
            $('#modalLine').modal('hide');
        })

   })
   
</script>