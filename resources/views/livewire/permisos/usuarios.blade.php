 <div class="tab-pane fade {{ $tab == 'usuarios' ? 'show active' : '' }}" id="usuarios_content" role="tabpanel" > <!--//*  LLAVES-->

   <div class="row">
    <div class="col-lg-12 col-sm-12 col-md-7">
     <h6 class="text-center"><b>USUARIOS REGISTRADOS</b></h6> 
    </div>
    <div class="table-responsive">
      <table id="tblPermisos" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
        <thead>
          <tr> 
            <th class="text-center col-3">Nombre Usuario</th>                                       
            <th class="text-center col-2">Nick Name </th>                                       
            <th class="text-center col-5">Rol Asignado</th>                                       
            <th class="text-center col-2">ACCIONES</th>
          </tr>
        </thead>
      <tbody>
       @foreach($usuarios as $u)
       <tr>                                    
        <td>{{$u->name}}</td>
        <td>{{$u->email}}</td>
        <td>{{$u->misroles}}</td>
        <td class="text-center">
          <ul class="table-controls">
            <li>
              <a href="javascript:void(0);" wire:click="agregaUser()"
                  data-toggle="tooltip" data-placement="top" title="Agregar Usuario">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="BROWN" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
              </a>
            </li>
            <li>
              <a href="javascript:void(0);" wire:click="editaUser('{{$u->id}}')"
                  data-toggle="tooltip" data-placement="top" title="Editar Usuario">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              </a>
            </li>
            <li>
              <a href="javascript:void(0);" onclick="anulaUser('{{$u->id}}')"
                  data-toggle="tooltip" data-placement="top" title="DesActivar Usuario">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
              </a>
            </li>

          </ul>
        </td>



    </tr>
    @endforeach
  </tbody>
</table>
</div>    
</div>

</div>
</div>
</div>