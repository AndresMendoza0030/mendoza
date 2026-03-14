<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use App\User;
use App\Warehouse;
use App\Destino;
use DB;


class PermisosController extends Component
{
	public $permisoTitle ='Crear',$roleTitle ='Crear', $userSelected;
	public $tab = 'roles', $roleSelected; //*

	public $clave,$loginuser,$nameuser,$idemple,$iduser, $bodegapred, $destinopred, $bodegas,$destinos;
	public $action =1;
	public $nuevous=0;


	public function render()
	{   		
		$this->bodegas  = Warehouse::select('*')->get(); //*		
		$this->destinos = Destino::select('*')->get(); //*				
		$roles = Role::select('*', DB::RAW("0 as checked "))->get();
		$permisos = Permission::select('*', DB::RAW("0 as checked "))->get(); //*
		$usuarios = User::select('id','name','email','idempleado','estatus',
		DB::RAW("'' as misroles"))
		->where('estatus','=',0)->get();
		
		if ($usuarios)
		{
			foreach ($usuarios as $us)
			{
				$mirol='';
				$rolez = $us->getRoleNames();
	
				if  ($rolez)
				{

					foreach ($rolez as $r)
					{
						$mirol = $mirol . TRIM($r).',';
					}
				}
				$us->misroles = $mirol;
			}
		}

	
		if($this->userSelected !='' && $this->userSelected!='Seleccionar')
		{ 
			foreach ($roles as $r) {
				$user = User::find($this->userSelected);				
				$tieneRole = $user->hasRole($r->name);
				if($tieneRole) {
					$r->checked = 1;
				}
			}
			
		}
		if($this->roleSelected !='' && $this->roleSelected!='Seleccionar' ) //*
		{ 
			foreach ($permisos as $p) {
				$role = Role::find($this->roleSelected);				
				$tienePermiso = $role->hasPermissionTo($p->name);
				if($tienePermiso) {
					$p->checked = 1;
				}
			}
			
		}
		
		return view('livewire.permisos.component', [
			'roles'     =>  $roles,
			'permisos'  =>  $permisos, 
			'usuarios'  => $usuarios,
		]);
	}


	/*-----------------------------------------------------------------------
								SECCION ROLES
	  -----------------------------------------------------------------------
	*/
	  public function resetInput()
	  {	  
	  	$this->roleTitle ='Crear';
	  	$this->permisoTitle ='Crear';
	  	$this->userSelected ='';  //*
	  	$this->roleSelected ='';  //*  	
	  }


	  public function CrearRole($roleName, $roleId)
	  {	  	
	  	if($roleId) 
	  		$this->UpdateRole($roleName, $roleId);		
	  	else
	  		$this->SaveRole($roleName);
	  }


	  public function SaveRole($roleName)
	  {

	  	$role = Role::where('name', $roleName)->first();

	  	if ($role) {	
	  		$this->emit('msg-error', 'El role que intentas registrar ya existe en sistema');
	  		return;
	  	}	

	  	Role::create([
	  		'name' => $roleName
	  	]);

	  	$this->emit('msg-ok', 'Se registró el role correctamente');
	  	$this->resetInput();	

	  }



	  public function UpdateRole($roleName, $roleId)
	  {    	
	  	
	  	$role = Role::where('name', $roleName)->where('id', '<>', $roleId)->first();      //*

	  	if ($role) {	                                                                   //*
	  		$this->emit('msg-error', 'El role que intentas registrar ya existe en sistema');
	  		return;
	  	}	                                                                               //*

	  	$role = Role::find($roleId);
	  	$role->name = $roleName;
	  	$role->save();
	  	$this->emit('msg-ok', 'Se actualizó el role correctamente');
	  	$this->resetInput();    	
	  }


	  public function destroyRole($roleId)
	  {	  	
	  	Role::find($roleId)->delete();
	  	$this->emit('msg-ok', 'Se eliminó el role correctamente');
	  }

	  public function agregaUser()
	  {
		$this->nuevous = 0;
		$this->action=2;		  
		$this->loginuser='';
		$this->nameuser='';		
		$this->idemple='';		
	  }

	  public function editaUser($id)
	  {
    	$record = User::findOrFail($id);
		$this->nuevous = 1;
		$this->iduser= $id;
		$this->action=2;		  
		$this->loginuser=$record->email;
		$this->nameuser=$record->name;		
		$this->idemple=$record->idempleado;		

	  }

	  public function grabaUser()
	  {
		if ($this->nuevous == 0)
		{
			$usuario = User::create([
			'name'           => $this->nameuser,
			'email'          => $this->loginuser,
			'destino_pred'	 => $this->destinopred,
			'bodega_pred'	 => $this->bodegapred,
			'password'		 => bcrypt('1111'),
			'estatus'		 => 0
			]);
		}
		if ($this->nuevous == 1)
		{
			$usuario = User::find($this->iduser);
			$usuario->update([
				'name'           => $this->nameuser,
				'email'          => $this->loginuser,
				'destino_pred'	 => $this->destinopred,
				'bodega_pred'	 => $this->bodegapred,
			]);
		}
		$this->action=1;		  


	  }

	  public function doAction($accion)
	  {
		$this->action=$accion;
		if ($accion==2)
		{
			$this->clave='';
			$this->loginuser='';
			$this->nameuser='';		
			$this->idemple=0;		
		}
	  }


	  public function AsignarRoles($rolesList)
	  {			
	  	if($this->userSelected > 0 ){
	  		$user = User::find($this->userSelected);	

	  		if($user) {						
	  			$user->syncRoles($rolesList);				
	  			$this->emit('msg-ok', 'Roles asignados correctamente');
	  			$this->resetInput();  
	  		}
	  	}
	  }




	  protected $listeners =[
	  	'destroyRole'     => 'destroyRole',
	  	'destroyPermiso'  => 'destroyPermiso',
	  	'CrearPermiso'    => 'CrearPermiso',
	  	'CrearRole'       => 'CrearRole',		
	  	'AsignarRoles'    => 'AsignarRoles',
	  	'AsignarPermisos'    => 'AsignarPermisos'
	  ];
	 


	/*-----------------------------------------------------------------------
  							SECCION PERMISOS  - AQUI
	  -----------------------------------------------------------------------
	*/

	  public function CrearPermiso($permisoName, $permisoId)
	  {

	  	if($permisoId) 
	  		$this->UpdatePermiso($permisoName, $permisoId);		
	  	else
	  		$this->SavePermiso($permisoName);
	  }

	  public function SavePermiso($permisoName)
	  {
	  	$permiso = Permission::where('name', $permisoName)->first();

	  	if ($permiso) {	
	  		$this->emit('msg-error', 'El permiso que intentas registrar ya existe en sistema');
	  		return;
	  	}	

	  	Permission::create(['name' => $permisoName]);	  	
	  	$this->emit('msg-ok', 'Permiso Registrado correctamente');
	  	$this->resetInput();
	  }


	  public function UpdatePermiso($permisoName, $permisoId)
	  {
	  	$permiso = Permission::where('name', $permisoName)->where('id','<>',$permisoId)->first();

	  	if ($permiso) {	
	  		$this->emit('msg-error', 'El permiso que intentas registrar ya existe en sistema');
	  		return;
	  	}

	  	$permiso = Permission::find($permisoId)	;
	  	$permiso->name = $permisoName;
	  	$permiso->save();	  	
	  	$this->emit('msg-ok', 'Permiso Actualizado correctamente');
	  	$this->resetInput();

	  }


	  public function destroyPermiso($permisoId)
	  {
	  	Permission::find($permisoId)->delete();	  	
	  	$this->emit('msg-ok', 'Permiso Eliminado correctamente');
	  }
	  

	  public function AsignarPermisos($permisosList, $roleId)
	  {	  	
	  	if($roleId > 0 ){
	  		$role = Role::find($roleId);	

	  		if($role) {						
	  			$role->syncPermissions($permisosList);				
	  			$this->emit('msg-ok', 'Permisos asignados correctamente');
	  			$this->resetInput();  
	  		}
	  	}
	  }


	}
