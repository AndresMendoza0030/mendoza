@if($action == 1)                
<div class="row layout-top-spacing">    
    <input type="hidden" id="estadoform" >
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h6><b>Catálogo de Kits/Perfiles</b></h6>
                    </div> 
                </div>
            </div>

            <div class="row justify-content-between mb-2 mt-2">
                @if($modonuevo==0)
	            <div class="col-lg-5 col-md-6 col-sm-12">
                    <h7 class="text-info">Buscar Kit/Perfil</h7>
		            <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                       </div>
                        <input type="text" wire:model="search" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
                    </div>
                </div>
                @endif

                @if($modonuevo==1)
                <div class="col-lg-5 col-md-12 col-sm-12 text-left">
                    <h7 class="text-info">Seleccione de Catálogo</h7>
                    <div class="input-group input-group-sm ">
                        <select wire:model="productoid"  id="selectproducto" onchange="mostrarDatos(1)" class="js-example-basic-single form-control">
                            <option value="">Elegir</option>
                            @foreach($datos as $pr)
                                <option value="{{$pr->id}}">
                                {{$pr->barra}} {{$pr->nombre}}
                                </option>                                       
                            @endforeach                              
                        </select>	
                    </div>
                </div>
                @endif

                <div class="col-lg-1 col-md-1 col-sm-12 mt-2 text-right">
         	        <button type="button" wire:click="nuevoPerfil()" class="btn btn-sm btn-dark">
         	 	        <i class="la la-file la-lg"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>                                                   
                            <th class="text-center col-2" style="font-size: .70rem;">NOMBRE</th>
                            <th class="text-center col-1" style="font-size: .70rem;">PRECIO VTA</th>
                            <th class="text-center col-2" style="font-size: .70rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $r)
                            <tr>
                                <td class="text-left col-2" style="font-size: .70rem;"><b>{{$r->nombre}}</b> </td>
                                <td class="text-right col-1" style="font-size: .60rem;">{{number_format($r->preventa,4)}}</td>

                                <td class="text-center">
                                    <ul class="table-controls">
                                    	<li>
                                            <a href="javascript:void(0);"          		
                                                wire:click="detallarPerfil({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Detalle Perfil">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-align-justify"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                                            </a>
	                                    </li>
                                    	<li>
                                            <a href="javascript:void(0);"          		
                                                onclick="confirm1({{$r->id}})" data-toggle="tooltip" data-placement="top" title="DesHabilitar Perfil">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
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
        @include('livewire.perfiles.datosestudiomodal')    
    </div>
</div>
@elseif($action == 2 )
    @include('livewire.perfiles.form')
@endif




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

    function updModo()
    {
        window.livewire.emit('resetEstudio')    
    }

    function updValores()
    {
        $('#datosestudiomodal').modal('hide')
        window.livewire.emit('updPerfil')    

    }
    
    function confirm1(id)
    {

       swal({
        title: 'CONFIRMAR',
        text: '¿DESEAS DESHABILITAR EL PERFIL?',
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
                window.livewire.emit('quitarPerfil', id)    
                swal.close()   
            })
        }


    function confirm2(id)
    {
       swal({
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL ESTUDIO DEL PERFIL?',
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
                window.livewire.emit('quitarEstudio', id)    
                //toastr.success('info', 'Registro eliminado con éxito')
                swal.close()   
            })
        }
    

    function mostrarDatos(md)
    {
        //idx= datosproducto[0];
        idx=$('#selectproducto').val();
        if (idx>0)
        {
            if (md==1)
            {
                window.livewire.emit('datosPerfil',idx);
            }
            if (md==2)
            {
                window.livewire.emit('datosProducto',idx);
            }
        }
        //
    }    

    document.addEventListener('DOMContentLoaded', function()
    {

        window.livewire.on('abre-Modal-Perfil', mdEstu =>
        {
            $('#mdperfil').val(mdEstu.perfil);
            $('#datosestudiomodal').modal('show')
        })

        window.livewire.on('select2', mdEstu =>
        {
            $('#selectproducto').select2();            
        })

    })

  
</script>
