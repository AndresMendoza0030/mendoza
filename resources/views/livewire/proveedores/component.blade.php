<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">

        @if($action == 1)                
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Listado de Proveedores</b></h5>
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
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-3">
                    <thead>
                        <tr>                                                   
                            <th class="text-center">ID</th>
                            <th class="text-center">NOMBRE DE PROVEEDOR</th>
                            <th class="text-center">CUENTA CONTABLE</th>
                            <th class="text-center">IDENTIFICACION</th>
                            <th class="text-center">ESTATUS</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $r)
                            <tr>
                                <td><p class="mb-0">{{$r->id}}</p></td>
                                <td class="text-center">{{$r->razon}}</td>
                                <td class="text-center">{{$r->cuentacontable}}</td>                                
                                <td class="text-center"><h7 class="text-info">Nit: </h7>{{$r->nit}} <h7 class="text-warning">NRC: </h7>{{$r->nrc}} <h7 class="text-success">DUI:</h7> {{$r->dui}}</td>
                                <td class="text-center">
                                @if($r->estatus==0)
                                <h7 class="text-success">ACTIVO</h7>
                                @else
                                <h7 class="text-warning">INACTIVO</h7>
                                @endif
                                </td>
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
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$info->links()}}
            </div>
        </div> 
        <input type="hidden" id="iaccion">
        @elseif($action == 2  || $action == 3)
            @include('livewire.proveedores.form')		
        @endif  
    </div>
</div>
@livewireScripts
<script >
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;
    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }

    function mascaraOn()
    {
        $('.dui').mask('00000000-0');
        $('.nit').mask('0000-000000-000-0');
        $('.movil').mask('0000-0000');
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
        //toastr.success('info', 'Registro eliminado con éxito')
        swal.close()
        })
    }

    function Edad()
    {
        var fechaNace = new Date($('#fechanace').val());
        var fechaActual = new Date()

        var mes = fechaActual.getMonth();
        var dia = fechaActual.getDate();
        var año = fechaActual.getFullYear();

        fechaActual.setDate(dia);
        fechaActual.setMonth(mes);
        fechaActual.setFullYear(año);

        edad = Math.floor(((fechaActual - fechaNace) / (1000 * 60 * 60 * 24) / 365));
    
        $('#edad').val(edad+'años')
    }

document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('msg-ok', dataMsg =>{ $('#modalLine').modal('hide')})

        $('#iaccion').val(1)       
        $('.js-example-basic-single').select2();
   })

</script>
