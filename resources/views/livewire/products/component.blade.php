@if($action == 1)                
<div class="row layout-top-spacing">    
    <input type="hidden" id="estadoform" >
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>CATALOGO DE PRODUCTOS</b></h5>
                    </div> 
                </div>
            </div>

            <div class="row justify-content-between mb-4 mt-3">
	            <div class="col-lg-4 col-md-4 col-sm-12">
		            <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                       </div>
                        <input type="text" wire:model="search" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-md-2 col-lg-2 col-sm-12 mt-2 mb-2 text-right mr-2">
         	        <button type="button" wire:click="doAction(2)" class="btn btn-dark">
         	 	        <i class="la la-file la-lg"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-3">
                    <thead>
                        <tr>                                                   
                            <th class="text-center col-1" style="font-size: .70rem;">ID</th>
                            <th class="text-center col-3" style="font-size: .70rem;">NOMBRE</th>
                            <th class="text-center col-2" style="font-size: .70rem;" >CLASE</th>
                            <th class="text-center col-2" style="font-size: .70rem;">LINEA</th>
                            <th class="text-center col-1" style="font-size: .70rem;">PR.COSTO</th>
                            <th class="text-center col-2" style="font-size: .70rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $r)
                            <tr>
                                <td class="text-left col-1" style="font-size: .70rem;"><b>{{$r->id}}</b> </td>
                                <td class="text-left col-3" style="font-size: .70rem;"><b>{{$r->nombre}}</b> </td>                                
                                <td class="text-left col-1" style="font-size: .60rem;">{{$r->laclase}}</td>
                                <td class="text-left col-1" style="font-size: .60rem;">{{$r->lalinea}}</td>
                                <td class="text-right col-1" style="font-size: .70rem;">{{number_format($r->costo,4)}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                    	<li>
                                        <a href="javascript:void(0);"          		
                                            wire:click="edit({{$r->id}})"
		                                    data-toggle="tooltip" data-placement="top" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>

	                                    </li>
	                                    <li>
		                                    <a href="javascript:void(0);"          		
		                                    onclick="Confirm('{{$r->id}}')"
		                                    data-toggle="tooltip" data-placement="top" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                    	</li>
	                                    <li>
		                                    <a href="javascript:void(0);"   wire:click="verExistencia({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Ver Existencia">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="purple" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </a>
                                    	</li>
	                                    <li>
		                                    <a href="javascript:void(0);" wire:click="verMovimientoProd({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Ver Movimiento">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
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
</div>
@elseif($action == 2 )
    @include('livewire.products.form')		
@else 
    @include('livewire.products.movimientoform')		
@endif

@include('livewire.products.stockform')		




@livewireScripts

<script type="text/javascript">
    var concambios=false;
    var bPreguntar = true;
    window.onbeforeunload = hayCambios; //preguntarAntesDeSalir;

    function hayCambios()
    {
         if (concambios)  
	    return 'Puede haber cambios sin guardar en el formulario, ¿Desea salir de página?'; 
    }

    function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }


    function Confirm(id)
    {

       let me = this
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
            function()
            {
                console.log('ID', id);
                window.livewire.emit('deleteRow', id)    
                //toastr.success('info', 'Registro eliminado con éxito')
                swal.close()   
            })
        }
    
    function cerrarModal()
    {
        window.livewire.emit('doAction', 1)    
    }


    document.addEventListener('DOMContentLoaded', function()
    {
        window.livewire.on('mostrarExistencia', mostrarExistencia =>
        {
            $('#modalExiste').modal('show')
        })
    })

    
    
</script>
