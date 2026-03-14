<div class="layout-px-spacing">


    <div class="row layout-top-spacing">
        
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one">


                   <ul class="nav nav-pills mb-3 mt-3" role="tablist">

                   <li class="nav-item ">
                        <a class="nav-link {{ $tab == 'usuarios' ? 'active' : '' }}" wire:click="$set('tab', 'usuarios')" id="tabUsuarios"  data-toggle="pill" href="#usuarios_content" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="BROWN" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                            USUARIOS
                        </a>
                    </li>                                       

                    <li class="nav-item ">
                        <a class="nav-link {{ $tab == 'roles' ? 'active' : '' }}" wire:click="$set('tab', 'roles')" id="tabRoles"  data-toggle="pill" href="#roles_content" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-award"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            ROLES
                        </a>
                    </li>                                       

                    <li class="nav-item ">
                        <a class="nav-link {{ $tab == 'permisos' ? 'active' : '' }}" wire:click="$set('tab', 'permisos')" id="tabPermisos"  data-toggle="pill" href="#permisos_content" role="tab"  >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="RED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-key"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                            PERMISOS
                        </a>
                    </li>

                </ul>
                <div class="tab-content" >

                @if($action == 2)
                    @include('livewire.permisos.userform')
                @endif

                @if($tab == 'usuarios')
                    @include('livewire.permisos.usuarios')
                @endif
                @if($tab == 'roles')
                    @include('livewire.permisos.roles')
                @else ($tab == 'permisos')
                    @include('livewire.permisos.permisos')                  
                @endif



               </div>

           </div>

       </div>
   </div>
</div>
</div>



<script type="text/javascript">

    function showRole(role)
    {
        var data = JSON.parse(role)
        $('#roleName').val(data['name'])
        $('#roleId').val(data['id'])        
    }
    function clearRoleSelected()
    {
        $('#roleName').val('')
        $('#roleId').val(0) 
        $('#roleName').focus()
    }

    function showPermission(permission)
    {
        var data = JSON.parse(permission)
        $('#permisoName').val(data['name'])
        $('#permisoId').val(data['id'])        
    }
    function clearPermissionSelected()
    {
        $('#permisoName').val('')
        $('#permisoId').val(0) 
        $('#permisoName').focus()
    }

    function Confirm(id,eventName)
    {       
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
        window.livewire.emit(eventName, id)    
        toastr.success('info', 'Registro eliminado con éxito')
        $('#permisoId').val(0)
        $('#permisoName').val('')
        $('#roleId').val(0)
        $('#roleName').val('')
        swal.close()   
    })


   }

   function AsignarRoles()
   {
    console.clear()  
    
    var rolesList = [];
    $('#tblRoles').find('input[type=checkbox]:checked').each(function () {  
        rolesList.push(($(this).attr("data-name")));  
    });
    console.log(rolesList)

    if(rolesList.length < 1 ) {
        toastr.error('', 'Selecciona al menos un role')
        return;
    }
    else if( $('#userId option:selected').val() == 'Seleccionar' )
    {
        toastr.error('', 'Selecciona el usuario')
        return;
    }

    window.livewire.emit('AsignarRoles', rolesList)
}

function AsignarPermisos()
{
    if( $('#roleSelected option:selected').val() == 'Seleccionar' ) //*
    {
        toastr.error('', 'Selecciona el role')
        return;
    }  

    
    var permisosList = [];
    $('#tblPermisos').find('input[type=checkbox]:checked').each(function () {  
        permisosList.push(($(this).attr("data-name")));  
    });
    

    if(permisosList.length < 1 ) {
        toastr.error('', 'Selecciona al menos un permiso')
        return;
    }
    

    window.livewire.emit('AsignarPermisos', permisosList, $('#roleSelected option:selected').val()) //*
}

document.addEventListener('DOMContentLoaded', function () { 

    

    window.livewire.on('msg-ok', msgText => {
       $('#permisoId').val(0)
       $('#permisoName').val('')
       $('#roleId').val(0)
       $('#roleName').val('')
   })


    $('body').on('click','.check-all', function(){ //*

       var state = $(this).is(':checked') ? true : false

       $("#tblPermisos").find('input[type=checkbox]').each(function (e) {         

        $(this).prop('checked', state)
        
    })

   })


})

</script>