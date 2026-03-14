<div class="row layout-top-spacing" >
	<div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
		<div class="widget-content-area br-4">
			<div class="widget-one">
				<!--div titulo y boton regersar -->
				<div class="row">
					<div class="col-2">
						<button class="btn btn-sm btn-dark" wire:click="doAction(1)">
							<i class="la la-chevron-left"></i>
						</button>
					</div>
					<div class="col-8">
						<h6 class="text-center"><b>DETALLE DEL PERFIL: {{$elperfil}}</b></h6>
					</div>
				</div>  

				<div class="row">                     
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="card">
								<div class="col-lg-12 col-md-12 col-12 layout-spacing mr-2 mt-2">
									<div class="widget-content-area br-4">
										<div class="row">
											<div class="col-lg-12 col-md-12 col-12 layout-spacing">								
												<table class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-3">
													<thead>
														<tr>                                                   
															<th class="text-center col-2" >ID - PRODUCTO/SERVICIO</th>
															<th class="text-center col-1" >P.VENTA</th>
															<th class="text-center col-1" >% DESCUENTO</th>
															<th class="text-center col-2" >ACCION</th>											
														</tr>
													</thead>
													<tbody>
														@foreach($detalle as $r)
															<tr>
																<td class="text-left" >{{$r->producto_id}} - {{$r->nombre}} </td>
																<td class="text-right">{{number_format($r->precio,4)}}</td>
																<td class="text-center" >{{number_format($r->descuento,4)}}</td>
																
																<td class="text-center">
																	<ul class="table-controls ml-1">
																		<li>
																		<a href="javascript:void(0);" onclick="confirm2({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Quitar Estudio">
																		<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
																		</a>
																		</li>

																	</ul>
																</td>
															</tr>
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<td></td>
															<td class="text-right"><b>{{number_format($tprecio,4)}}</b></td>
															<td ></td>
														</tr>

													</tfoot>
												</table>
											</div>
										</div>

										<div class="col-lg-12 col-md-12 col-sm-12 mt-2 text-right">
         	        						<button type="button" wire:click="nuevoEstudio()" class="btn btn-sm btn-success">
         	 	        						<i class="la la-file la-lg"></i>Agrega Estudio
                    						</button>
                						</div>

									</div>
								</div>
						</div>
					</div>
				</div>
				@if($modoagregar==1)
				<div class="row">                     
					<div class="col-lg-12 col-md-12 col-sm-12 mt-3">
						<div class="card">
							<div class="row mr-2 ml-2">
								<div wire:ignore class="col-lg-4 col-md-12 col-sm-12 text-left">
									<h7 class="text-info">Seleccione de Catálogo</h7>
									<div class="input-group input-group-sm ">
										<select wire:model="productoid"  id="selectproducto" onchange="mostrarDatos(2)" class="js-example-basic-single form-control">
											<option value="">Elegir</option>
											@foreach($datos as $pr)
											<option value="{{$pr->id}}">
											{{$pr->barra}} {{$pr->nombre}}
											</option>                                       
											@endforeach                              
										</select>	
									</div>
								</div>

								<div class="col-lg-2 col-md-4 col-sm-12">
									<h7 class="text-info">Precio en Pérfil</h7>
									<div class="input-group input-group-sm mb-2 mr-sm-2">
										<input  type="text" wire:model.defer='precioprod' 
										id="precioprod" onkeypress="return CheckNumeric()" class="form-control">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-sm-12">
									<h7 class="text-info">% Descto. Perfil</h7>
									<div class="input-group input-group-sm mb-2 mr-sm-2">
										<input  type="text" wire:model.defer='descuentoprod' 
										id="descuentoprod" onkeypress="return CheckNumeric()" class="form-control">
									</div>
								</div>

								<div class="form-group col-lg-3 col-md-3 col-sm-12  mt-4">
									<button tabindex=6 class="btn btn-sm btn-dark"  wire:click="saveProd()" >
									<i class="la la-edit la-lg"></i>Agregar
									</button>
									<button class="btn btn-sm btn-danger"  wire:click="descartaProd()" >
									<i class="la la-edit la-lg"></i>Descartar
									</button>
								</div>

							</div>


						</div>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
  		$('.numero4').mask('999,999,999.9999', {reverse: true});
		
	});

</script>
