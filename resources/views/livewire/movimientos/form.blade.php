<!--<section id="datosRegistro">  -->
    <div class="row layout-top-spacing" >
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                    <!--div titulo y boton regersar -->
                    <div class="row">
                    	<div class="col-2">
                        	<button class="btn btn-dark" wire:click="doAction(1)">
                            	<i class="la la-chevron-left"></i>
                        	</button>
                    	</div>
                    	<div class="col-8">
	                        <h5 class="text-center"><b>DATOS DEL MOVIMIENTO</b></h5>
    	                </div>
        	        </div>  

            		<!--div datos de producto/servicio -->
             		<div class="row">                     
		                <div class="form-group col-lg-3 col-md-4 col-sm-12">
        		            <h7 class="text-info">Tipo:</h7>
                    		<div class="input-group input-group-sm">
		                        <input type="text" disabled wire:model.lazy="eldocumento"  class="form-control">
        		            </div>
		                </div>
                		<div class="form-group col-lg-3 col-md-4 col-sm-12">
                    		<h7 class="text-info">Bodega:</h7>
                    		<div class="input-group input-group-sm">
                        		<input type="text" disabled wire:model.lazy="labodega" class="form-control">
                    		</div>
                		</div>
                		<div class="form-group col-lg-2 col-md-4 col-sm-12">
                    		<h7 class="text-info">Fecha:</h7>
                    		<div class="input-group input-group-sm">
                        		<input type="text" disabled wire:model.lazy="lafecha" class="form-control">
                    		</div>
                		</div>
                		<div class="form-group col-lg-4 col-md-4 col-sm-12">
                    		<h7 class="text-info">Concepto:</h7>
                    		<div class="input-group input-group-sm">
                        		<input type="text" disabled wire:model.lazy="elconcepto" class="form-control">
                    		</div>
                		</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-8 col-md-12 col-sm-12">
								<h7 class="text-info">Información:</h7>
								<div class="input-group input-group-sm">
									<input type="text" disabled wire:model.lazy="sujeto"  class="form-control">
								</div>
						</div>
						<div class="form-group col-lg-3 col-md-4 col-sm-12">
								<h7 class="text-info">Usuario:</h7>
								<div class="input-group input-group-sm">
									<input type="text" disabled wire:model.lazy="usuario"  class="form-control">
								</div>
						</div>
					</div>

					<div class="row">
                		<div class="table-responsive mt-3">
                			<table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                    			<thead>
			                        <tr>                                                   
                            			<th class="text-center">ID Producto</th>
                            			<th class="text-center">NOMBRE DEL PRODUCTO</th>
                            			<th class="text-center">Cantidad</th>
										<th class="text-center">Costo Unitario</th>										
                            			<th class="text-center">Total</th>
                        			</tr>
                    			</thead>
                    			<tbody>
                        		@foreach($detamov as $dm)
                            		<tr>
                                		<td class="text-center">{{$dm->producto_id}}</td>
                                		<td class="text-center">{{$dm->producto}}</td>
                                		<td class="text-center">{{$dm->cantidad}}</td>
										<td class="text-center">{{number_format($dm->precosto,4)}}</td>
                                		<td class="text-center">{{number_format($dm->gravado,4)}}</td>
		                            </tr>
        		                @endforeach
                		        </tbody>
                    		</table>
                		</div>
            		</div>
				</div>
			</div>
  		</div>
	</div>
<!-- </section> -->

