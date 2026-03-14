 <div class="tab-pane fade {{ $tab == 'permisos' ? 'show active' : '' }}" id="permisos_content" role="tabpanel" > <!--//*  LLAVES-->

   <div class="row">
    <div class="col-sm-12 col-md-7">
     <h6 class="text-center"><b>PERMISOS DE SISTEMA</b></h6> 
     <div class="input-group"> 
       <div class="input-group-prepend">
         <span onclick="clearPermissionSelected()" class="input-group-text" style="cursor: pointer;">
          <i class="la la-remove la-lg"></i>
        </span>                           
      </div>  
      <input type="text" id="permisoName"  class="form-control" autocomplete="off">
      <input type="hidden" id="permisoId">      
      <div class="input-group-prepend">        

        <span wire:click="$emit('CrearPermiso',$('#permisoName').val(),$('#permisoId').val())" class="input-group-text">
          <i class="la la-save la-lg"></i>
        </span>

      </div>
    </div>
    <div class="table-responsive">
      <table id="tblPermisos" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
        <thead>
          <tr> 
            <th class="">DESCRIPCIÓN</th>                                       
            <th class="text-center">ROLES </br>con el permiso </th>                                       
            <th class="text-center">ACCIONES</th>
            <th class="text-center">
              <!--//*  DIV N-CHECK-->
             <div class="n-chk">
              <label class="new-control new-checkbox checkbox-primary">
                <input  type="checkbox" class="new-control-input check-all"> <!--//*    CHECK-ALL-->
                <span class="new-control-indicator"></span>TODOS
              </label>
            </div>

           
          </th>
        </tr>
      </thead>
      <tbody>
       @foreach($permisos as $p)
       <tr>                                    
        <td>{{$p->name}}</td>
        <td class="text-center">{{\App\User::permission($p->name)->count()}}</td>

        <td class="text-center">                                               

          <span style="cursor: pointer;" 
          onclick="showPermission('{{$p}}')"
          ><i class="la la-edit la-2x text-warning"></i></span>




          @if(\App\User::permission($p->name)->count() <=0 )
          <a href="javascript:void(0);"               
          onclick="Confirm('{{$p->id}}','destroyPermiso')"
          title="Delete"><i class="la la-trash la-2x text-danger"></i>
        </a>                                                    
        @endif


      </td>
      <td class="text-center">
        <div class="n-chk">
          <label class="new-control new-checkbox checkbox-primary">
            <input  id="p{{$p->id}}" data-name="{{$p->name}}"  type="checkbox" class="new-control-input check-permiso" 
            {{$p->checked == 1 ? 'checked' : '' }} >
            <span class="new-control-indicator"></span>Asignar
          </label>
        </div>
      </td>

    </tr>
    @endforeach
  </tbody>
</table>
</div>    
</div>
<div class="col-sm-12 col-md-5">
  <h6 class="text-left">Elegir Role</h6> 
  <div class="input-group">
    <select id="roleSelected" wire:model="roleSelected"  class="form-control text-center" > 
     <option value="Seleccionar">Seleccionar</option> <!--//* -->
     @foreach($roles as $r)
     <option value="{{$r->id}}">{{$r->name}}</option>  
     @endforeach            
   </select>               
 </div>

 <button type="button" onclick="AsignarPermisos()" class="btn btn-primary mt-4">Asignar Permisos</button>

</div>
</div>
</div>