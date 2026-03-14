<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
    @if($action==1)
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Visor Movimientos de Inventarios</b></h5>
                    </div> 
                </div>
            </div>
            <!-- Buscador -->
            <!--div datos head documento -->
            <div class="row">                     
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="input-group input-group-sm ">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGrouptipomovimiento">Movimiento</label>
                        </div>
                        <select class="form-control" wire:model="tipodoc" id="tipodoc">
                            <option value="">Elegir</option>
 	    				    @foreach($tipomovs as $tm)
 							    <option value="{{ $tm->documento}}">
 						            {{$tm->nombre}}
 							    </option>                                       
						    @endforeach                              
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12">
                    <div class="input-group input-group-sm ">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGrouptipomovimiento">Bodega</label>
                        </div>
                        <select class="form-control" wire:model="bodega" id="bodega">
                            <option value="">Elegir</option>
 	    				    @foreach($bodegas as $bd)
 							    <option value="{{ $bd->id}}">
 						            {{$bd->nombre}}
 							    </option>                                       
						    @endforeach                              
                        </select>
                    </div>
                </div>
	            <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="input-group input-group-sm ">
                        <div class="input-group-prepend">
                            <label class="input-group-text" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="17" height="17" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </label>
                        </div>
                        <input type="date" aria-label="Desde" class="form-control"  wire:model="desde" >
                        <input type="date" aria-label="Hasta" class="form-control"  wire:model="hasta" >
                    </div>
                </div>
            </div>
            <!--GridPacientes -->
            <div class="row">
                <div class="table-responsive mt-3">
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                    <thead>
                        <tr>                                                   
                            <th class="text-center">ID Mov</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">CONCEPTO</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $ls)
                            <tr>
                                <td class="text-center">{{$ls->id}}
                                @if($ls->reversado=="SI") Revsdo @endif</td>
                                <td class="text-center">{{$ls->fecha}} - {{$ls->nusuario}}</td>
                                <td class="text-center">{{$ls->concepto}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                    	<li>
                                        <a href="javascript:void(0);" wire:click="muestraInfoMov({{$ls->id}})"	
		                                    data-toggle="tooltip" data-placement="top" title="Ver Detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="brown" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </a>
	                                    </li>
                                    	<li>
                                        <a href="javascript:void(0);" onclick="confirmaReversar('{{$ls}}')"         		
		                                    data-toggle="tooltip" data-placement="top" title="Reversar Movimiento">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="purple" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>                                            
                                        </a>
	                                    </li>
                                    	<li>
                                        <a href="/detallemov/{{$ls->id}}" target="_blank"   
		                                    data-toggle="tooltip" data-placement="top" title="Imprimir Detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        </a>
	                                    </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$info->links()}}

                </div>
            </div>



        </div> 
    @elseif($action == 2)
       @include('livewire.movimientos.form')		
    @endif  
   </div>
</div>

@livewireScripts

<script type="text/javascript">

function confirmaReversar(reg)
{
    var info = JSON.parse(reg);
    $('#id').val(info.id)

       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS REVERSAR :'+info.cdocumento+":"+info.id+" ?",
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
            window.livewire.emit('reversaMovimiento', info.id)    
            swal.close()
        })
}

document.addEventListener('DOMContentLoaded', function()
{
    window.livewire.on('msg-ok', dataMsg =>{ $('#modalLine').modal('hide')})
    $('#selectproducto').select2();
})

</script>