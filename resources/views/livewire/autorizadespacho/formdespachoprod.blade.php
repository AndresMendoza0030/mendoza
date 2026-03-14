<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
					<div class="col-2">
						<button class="btn btn-dark" wire:click="setAction(1)">
							<i class="la la-chevron-left"></i>
						</button>
					</div>

                    <div class="col-xl-12 text-center">
                        <h5>Detalle del documento para :<b>{{$xdocumento}}</b></h5>
                    </div> 
                </div>
            </div>
			<div class="row">
				<div class="form-group col-lg-3 col-md-4 col-sm-12">
					<h7 class="text-info">Bodega Despacha:</h7>
					<div class="input-group input-group-sm">
						<input type="text" disabled wire:model.lazy="despachabod"  class="form-control">
					</div>
				</div>
				<div class="form-group col-lg-4 col-md-4 col-sm-12">
					<h7 class="text-info">Destino:</h7>
					<div class="input-group input-group-sm">
						<input type="text" disabled wire:model.lazy="eldestino" class="form-control">
					</div>
				</div>
				<div class="form-group col-lg-3 col-md-4 col-sm-12">
					<h7 class="text-info">Fecha Solicitud y Solicitante:</h7>
					<div class="input-group input-group-sm">
						<input type="text" disabled wire:model.lazy="lafechadoc" class="form-control">
					</div>
				</div>
                <div class="form-group col-lg-2 col-md-6 col-sm-12 mt-3">
                    <button onclick=confirmaAutoriza()  type="button" class="btn btn-sm btn-primary">Autorizar Documento</button>
                </div>

			</div>
            <div class="row">                     
                <div class="col-lg-12">
                    <div class="card text-center">
                        <h6 class="card-header"><b>Detalle de Solicitud</b></h6>
                    </div>
                    <div class="table-responsive-sm ">
                        <table id="tbcargo" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                            <thead>
                                <tr>                                                   
                                    <th class="text-center col-2">ACCIONES</th>
                                    <th class="text-center col-5">DESCRIPCIÓN</th>
                                    <th class="text-center col-2">STOCK</th>
                                    <th class="text-center col-2">SOLICITADO</th>
                                    <th class="text-center col-2">AUTORIZADO</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($info) > 0)
                            @foreach($info as $d)
                                <tr>
									<td class="text-center" >
		                                <ul class="table-controls">
											<li>
												<a href="javascript:void(0);"          		
													onclick="Confirm('{{$d['iddetalle']}}','{{$d['producto']}}')"
													data-toggle="tooltip" data-placement="top" title="Quitar Item">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line></svg>												
												</a>
											</li>
											<li>
												<a href="javascript:void(0);"          		
													wire:click="editaLinea('{{$d['reg']}}')"
													data-toggle="tooltip" data-placement="top" title="Editar Cantidad Solicitada">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
												</a>
											</li>
											<li>
												<a href="javascript:void(0);"          		
													wire:click="autorizaLinea('{{$d['reg']}}')"
													data-toggle="tooltip" data-placement="top" title="Autorizar Cantidad Solicitada">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
												</a>
											</li>


	                                    </ul>
    	                            </td>
									<td class="text-left">
										<b>{{$d['productoid']}}</b> - {{$d['producto']}}
									</td>
                                	<td class="text-center">
										{{$d['stock']}}
									</td>
                                    <td class="text-center">
										{{$d['solicitado']}}
									</td>
                                    <td class="text-center">
                                        <b>{{$d['autorizado']}}</b>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

	    </div>
        @include('livewire.autorizadespacho.modalEdicion')		
    </div>
</div>

@livewireScripts
<script type="text/javascript">
  
</script>