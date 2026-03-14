<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Catálogo de Fabricantes</b></h5>
                    </div> 
                </div>
            </div>

            <!-- Buscador -->
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
     	                    <button type="button" onclick="openModal()" class="btn btn-dark">
     	 	                    <i class="la la-file la-lg"></i>
                            </button>
                        </div>

                </div>
            <!-- Buscador -->

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                    <thead>
                        <tr>                                                   
                            <th class="">ID</th>
                            <th class="">DESCRIPCIÓN</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $r)
                            <tr>
                                <td><p class="mb-0">{{$r->id}}</p></td>
                                <td>{{$r->nombre}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                    	<li>
                                        <a href="javascript:void(0);"          		
		                                    onclick="editManufacturer('{{$r}}')"
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
        @include('livewire.manufacturers.modal')    
        <input type="hidden" id="id" value="0">
    </div>
</div>

@livewireScripts

<script type="text/javascript">
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

   function editManufacturer(row)
   {
       var info = JSON.parse(row);


       $('#id').val(info.id)
       $('#nombre').val(info.nombre)
       $('.modal-title').text('Editar Fabricante')
       $('#modalClass').modal('show')
   }
  

   function openModal()
   {
    $('#id').val(0)
    $('#nombre').val('')
    $('.modal-title').text('Crear Fabricante')
    $('#modalClass').modal('show')
   }

   function saveManufacturer()
   {
       if($.trim($('#nombre').val()) == '')
       {
           toastr.error('No puede dejar el nombre en blanco...')
           return;
       }
       var datos = JSON.stringify(
           {
           'id'     : $('#id').val(),
           'nombre' : $('#nombre').val()
       })
       

       window.livewire.emit('createModal',datos)
       //window.livewire.emit('createFromModal',datos);
   }

   document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('msg-ok', dataMsg =>{ $('#modalClass').modal('hide')})

   })
   

</script>