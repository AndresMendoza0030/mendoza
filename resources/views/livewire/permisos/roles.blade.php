 <div class="tab-pane fade {{ $tab == 'roles' ? 'show active' : '' }} " id="roles_content" role="tabpanel" > <!--//*  LLAVES-->
  <div class="row mt-5">
    <!--columna tabla-->
    <div class="col-sm-12 col-md-7">
      <h6 class="text-center"><b>LISTADO DE ROLES</b></h6>  
      
      <div class="input-group">  
       <div class="input-group-prepend">
        <span onclick="clearRoleSelected()" class="input-group-text" style="cursor: pointer;">
          <i class="la la-remove la-lg"></i>
        </span>      
      </div>                            
      <input id="roleName" type="text" class="form-control" autocomplete="off">
      <input type="hidden" id="roleId">
      <div class="input-group-prepend">   
     
     
        <span wire:click="$emit('CrearRole',$('#roleName').val(),$('#roleId').val())" class="input-group-text">
          <i class="la la-save la-lg"></i>
        </span>
        
      </div>                        
    </div>                        

    <div class="table-responsive">
      <table id="tblRoles" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
        <thead>
          <tr> 
            <th class="">DESCRIPCIÓN</th>                                       
            <th class="text-center">USUARIOS </br>con el role </th>
            <th class="text-center">ACCIONES</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody>
         @foreach($roles as $r)
         <tr>                                    
          <td>{{$r->name}}</td>
          <td class="text-center">{{\App\User::role($r->name)->count()}}</td>

          <td class="text-center">

            <span style="cursor: pointer;" 
            onclick="showRole('{{$r}}')"
            ><i class="la la-edit la-2x text-warning"></i></span>



            @if(\App\User::role($r->name)->count() <=0 )                                             
            <a href="javascript:void(0);"               
            onclick="Confirm('{{$r->id}}','destroyRole')"
            title="Delete"><i class="la la-trash la-2x text-danger"></i></a> 
            @endif        

          </td>

          <td class="text-center">
            <div class="n-chk" id="divRoles">
              <label class="new-control new-checkbox checkbox-primary">
                <input data-name="{{$r->name}}"   type="checkbox"  
                class="new-control-input checkbox-rol" 
                {{$r->checked == 1 ? 'checked' : '' }} 
                >
                <span class="new-control-indicator"></span>
                Asignar
              </label>

            </div>
          </td>

        </tr>
        @endforeach
      </tbody>
    </table>

  </div>

</div>
<!--columna select  y boton-->
<div class="col-sm-12 col-md-5">
  <h6 class="text-left"><b>Elegir Usuario</b></h6> 
  <div class="input-group">
    <select id="userId" wire:model="userSelected"  class="form-control" > 
      <option value="Seleccionar">Seleccionar</option>
      @foreach($usuarios as $u)
      <option value="{{$u->id}}">{{$u->name}}</option>                                 
      @endforeach            
    </select>               
  </div>
  <button type="button" onclick="AsignarRoles()"  class="btn btn-primary mt-4" >Asignar Roles
  </button>

</div>

</div>
</div>
