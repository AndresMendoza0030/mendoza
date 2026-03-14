<!--DATOS CONTEO  -->
@if($action==2)
<div class="row layout-top-spacing" >
	<div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
		<div class="widget-content-area br-4">
			<div class="widget-one">
				<div class="row">
					<div class="col-12">
						<h6 class="text-center"><b>Conteo de Producto x Bodega</b></h6>
					</div>
				</div>

			
				<div class="row justify-content-between mb-2 mt-2">
 					<div class="form-group col-lg-3 col-md-12 col-sm-12">
						<h7 class="text-info">Fecha </h7>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div  class="input-group-text">
									<i class="la la-calendar la-lg"></i>
								</div>
							</div>
							@if($mconteo=="S")
								<input type="date"  wire:model.lazy="lfecha"  disabled class="form-control">							
							@else
								<input type="date"  wire:model.lazy="lfecha"  class="form-control">
							@endif
						</div>
					</div>
					<div class="form-group col-lg-4 col-md-12 col-sm-12">
						<h7 class="text-info">Concepto</h7>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<i class="la la-edit la-lg"></i>
								</div>
							</div>
							<input type="text" wire:model.lazy="lconcepto" class="form-control">
						</div>
					</div>
					<div class="form-group col-lg-3 col-md-12 col-sm-12">
						<h7 class="text-info" >Bodega Conteo</label>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div class="input-group-text">
									@if($mconteo=="S")
										<a href="javascript:void(0);" disabled wire:click="doAction(1)"
										data-toggle="tooltip" data-placement="top" title="Regresar a Pantalla Pcpal">
										<i class="la la-chevron-left"></i></a>
									@else
										<a href="javascript:void(0);"  wire:click="doAction(1)"
										data-toggle="tooltip" data-placement="top" title="Regresar a Pantalla Pcpal">
										<i class="la la-chevron-left"></i></a>
									@endif
								</div>
							</div>
							@if($mconteo=="S")							
									<select disabled wire:model="lbodega" class="form-control text-center">
									<option value="" disabled>Elegir Bodega</option>
										@foreach($bodegas as $bd)
											<option  value="{{ $bd->id }}" >
											{{ $bd->nombre}}
											</option>
										@endforeach
								@else
									<select wire:model="lbodega" class="form-control text-center">
									<option value="" disabled>Elegir Bodega</option>
										@foreach($bodegas as $bd)
											<option  value="{{ $bd->id }}" >
											{{ $bd->nombre}}
											</option>
										@endforeach
								@endif

							</select>
							<div class="input-group-prepend">
								<div class="input-group-text">
								@if($mconteo=="S")
									<a href="javascript:void(0);" wire:click="preparaConteo()"
									data-toggle="tooltip" data-placement="top" title="Iniciar Conteo">										
									<i class="la la-chevron-right"></i></a>
								@else
									<a href="javascript:void(0);" disabled wire:click="preparaConteo()"
									data-toggle="tooltip" data-placement="top" title="Iniciar Conteo">										
									<i class="la la-chevron-right"></i></a>

								@endif
								</div>
							</div>

						</div>
					</div>
				</div>
				@if ($mconteo=="S")
                	<div class="row  justify-content-between mb-2 mt-1">				
						<div class="col-lg-3 col-md-12 col-sm-12">
							<div class="input-group input-group-sm ">
								<div class="input-group-prepend">
									<span class="input-group-text" id="basic-addon1">
										<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
										</svg>
									</span>
								</div>
								<input type="text" wire:model="search2" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
							</div>
						</div>
						<div class="col-lg-8 col-md-12 col-sm-12">
							<div class="btn-group" role="group" >
								<button type="button" wire:click="agregaRegistro" class="btn btn-sm btn-success text-right mr-1 mt-1"
								data-toggle="tooltip" data-placement="top" title="Añadir Producto a Conteo">Añadir Producto
								</button>

								<button type="button" wire:click="grabaParcial" class="btn btn-sm btn-primary text-right mr-1 mt-1"
								data-toggle="tooltip" data-placement="top" title="Grabar Conteo">
								Grabar Conteo
								</button>

								<button type="button" onclick="confirmaFin('{{$movno}}')" class="btn btn-sm btn-secondary text-right mr-1 mt-1"
								data-toggle="tooltip" data-placement="top" title="Grabar Conteo">
								Finalizar Conteo
								</button>

								<button type="button" onclick="descartarConteo('{{$movno}}')" class="btn btn-sm btn-warning text-right mt-1"
								data-toggle="tooltip" data-placement="top" title="Descartar Conteo">Descartar Conteo
								</button>
							</div>
						</div>
					</div>

					@if($mdp=="S")
					<div class="row justify-content-between mb-3 mt-3" x-data="{ isOpen: true }" @click.away="isOpen = false">					
							<!--div buscador producto-->        
							<div class="form-group col-lg-6 col-md-12 " >
								<h7 class="text-info">Buscador de Producto</h7>
								<div class="input-group input-group-sm" >
									<div class="input-group-prepend">
										<div class="input-group-text">
											<a href="javascript:void(0);" wire:click="noAgregaRegistro()"
                                                    data-toggle="tooltip" data-placement="top" title="No Agregar Registro">
													<i class="la la-power-off la-lg"></i>
											</a>
										</div>
									</div>
									<input type="text" class="form-control" placeholder="Buscar Producto..."
									onkeyup="javascript:this.value=this.value.toUpperCase();"
									wire:model="lproducto" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
									@keydown.shift.tab="isOpen = false">
								</div>
								<ul class="list-group" x-show.transition.opacity="isOpen">
									@if($lproducto !='')
									@foreach($productos as $r)
									<li wire:click="seleProducto('{{$r}}')" class="list-group-item list-group-item-action">                               
										<b>{{$r->producto}}</b> - <h7 class="text-secondary">ID</h7>:{{$r->id}}
									</li>
									@endforeach
									@endif
								</ul>
							</div>
							<!--fin buscador producto-->
							@if($vct=="S")
							<div class="form-group col-lg-6 col-md-12 col-sm-12">
								<h7 class="text-info">Cantidad</h7>
								<div class="input-group input-group-sm">
									<input type="text" wire:model.lazy="lcantidad"  onkeypress="return CheckNumeric()" class="form-control">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<a href="javascript:void(0);" disabled wire:click="grabaRegistro()"
											data-toggle="tooltip" data-placement="top" title="Grabar Producto">
											<i class="la la-save la-lg"></i></a>
										</div>
									</div>
								</div>
							</div>
							@endif
						</div>
					@endif

					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12 col-12">
							<div class="table-responsive-md ">
                    			<table class="table table-sm table-responsive-sm table-bordered table-hover  table-highlight-head">
                    				<thead>
										<tr>                                                   
											<th class="text-center col-2 " style="font-size: .70rem;">Clase Producto</th>
											<th class="text-center col-3 " style="font-size: .70rem;">Nombre Producto</th>
											<th class="text-center col-2" style="font-size: .70rem;">Stock Sistema</th>
											<th class="text-center col-2" style="font-size: .70rem;">Conteo</th>
										</tr>
				                    </thead>
                				    <tbody >
									@if($mconteo=="S")
										@foreach($elconteo as $c)
											<tr >
												<td class="text-left" style="font-size: .70rem;"> {{$c->laclase}}</td>												
												<td class="text-left" style="font-size: .70rem;"> {{$c->producto_id}} - {{$c->producto}}</td>
												<td class="text-center" style="font-size: .70rem;">{{$c->actual}} </td>
												<td class="text-center" style="font-size: .70rem;">
													<input type="text" id="inp{{$c->id}}" autofocus class="col-xl-2 col-lg-2 col-md-7 col-xl-10 text-right form-control"
													 value="{{$c->contado}}" onkeypress="return CheckNumeric()"  style="font-size: .70rem;"
													 onchange="cambioEstado({{$c->id}},{{$c->producto_id}},{{$c->actual}})">
												</td>
											</tr>
										@endforeach
									@endif
				                    </tbody>
				                </table>
								{{$elconteo->links()}}
							</div>
						</div>							
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endif
<!-- </section> -->
@livewireScripts
<script type="text/javascript">

function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

</script>

