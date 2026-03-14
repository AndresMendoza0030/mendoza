<!--DATOS CONTEO  -->
@if($action==4)
<div class="row layout-top-spacing" >
	<div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing" x-data="{ isOpen: true }" @click.away="isOpen = false">
		<div class="widget-content-area br-4">
			<div class="widget-one">
				<div class="row">
					<div class="form-group col-lg-1 col-md-4 col-sm-12">
						<button class="btn btn-dark" wire:click="doAction(1)">
							<i class="la la-chevron-left"></i>
						</button>
					</div>

					<div class="col-10">
						<h5 class="text-center"><b>Conteo de Producto x Bodega: {{$nbodega}}</b></h5>
					</div>
				</div>

			
				<div class="row justify-content-between mb-2 mt-2">
 					<div class="form-group col-lg-3 col-md-12 col-sm-12">
						<h7 class="text-info">Fecha </h7>
						<div class="input-group">
							<div class="input-group-prepend">
								<div  class="input-group-text">
									<i class="la la-calendar la-lg"></i>
								</div>
							</div>
							<input type="date"  wire:model.lazy="lfecha"  disabled class="form-control">							
						</div>
					</div>
					<div class="form-group col-lg-4 col-md-12 col-sm-12">
						<h7 class="text-info">Concepto</h7>
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<i class="la la-edit la-lg"></i>
								</div>
							</div>
							<input type="text" disabled wire:model.lazy="lconcepto" class="form-control">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-12">
						<div class="table-responsive-md table-sm">
                    		<table class="table table-bordered table-hover table-striped table-checkable table-highlight-head">
								<thead>
									<tr>                                                   
										<th class="text-center col-4 ">Nombre Producto</th>
										<th class="text-center col-1">Stock Sistema</th>
										<th class="text-center col-2">Conteo</th>
									</tr>
								</thead>
								<tbody >
									@foreach($vconteo as $c)
										<tr >
											<td class="text-left"> {{$c->producto_id}} - {{$c->producto}}</td>
											<td class="text-right">{{$c->actual}} </td>

											@if($solovence=="S")
												<td class="text-center">{{$c->actual}} - {{$c->mes}}/{{$c->anno}}</td>
											@endif
											@if($solovence=="N")
											<td class="text-center">
											<div class="input-group">
												<input type="text" disabled class="col-3 form-control text-right"
													value="{{$c->contado}}" >
												<div class="input-group-append">													
													<button type="button" class="btn btn-outline-secondary">Stock.Vence:[{{$c->total}}]</button>
													<button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
													data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Ver
													</button>
													<div class="dropdown-menu">
														@if(count($fvences) > 0)
															@foreach($fvences as $vc)
																@if($vc->producto_id == $c->producto_id)
																	<a class="dropdown-item" href="#">
																	{{$vc->mes}}/{{$vc->anno}} : {{$vc->conteo}}</a>
																@endif
															@endforeach
														@endif
													</div>
												</div>
											</div>
											</td>
											@endif
										</tr>
									@endforeach
								</tbody>
				                </table>
								{{$vconteo->links()}}
							</div>
						</div>							
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
<!-- </section> -->
