<!--<section id="datosRegistro">  -->
    <div class="row layout-top-spacing" >
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing" x-data="{ isOpen: true }" @click.away="isOpen = false">
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
	                        <h5 class="text-center"><b>DATOS DE PRODUCTO/SERVICIO</b></h5>
    	                </div>
        	        </div>  

            		<!--div datos de producto/servicio -->
             		<div class="row mt-3">                     
		                <div class="form-group col-lg-5 col-md-5 col-sm-12">
        		            <h7 class="text-info">Nombre* </h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  wire:model.lazy="nombre"  class="form-control" maxlength="100"
								style="text-transform:uppercase"  placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
        		            </div>
                		    @error('nombre')
								<span class="text-danger">{{ $message }}</span>
							@enderror  
		                </div>
                		<div class="form-group col-lg-5 col-md-5 col-sm-12">
                    		<h7 class="text-info">Presentación</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="presentacion" class="form-control" maxlength="100"
								style="text-transform:uppercase"  placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                    		</div>
                		</div>
						<div class="col-md-2 col-lg-2 col-sm-12">Tipo
		                    <select wire:model="tipo"  class="form-control text-center" > 
								<option value="PRODUCTO">PRODUCTO</option>
 								<option value="SERVICIO">SERVICIO</option>
			            	</select>    
		                </div>
					</div>

					<div class="row mt-3">                     
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Clase Catálogo</label>
		 					<select wire:model="clase" onchange="MuestraLinea('{{$lineas}}');" class="form-control text-center">
 								<option value="Elegir" disabled="">Elegir</option>
 								@foreach($clases as $c)
 									<option value="{{ $c->id }}" >
 										{{ $c->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
 							@error('clase') <span class="text-danger">{{ $message }}</span> @enderror		               
 						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Linea Catálogo</label>
		 					<select wire:model="linea"  class="form-control text-center">
 								<option value="Elegir" disabled="">Elegir</option>
 								@foreach($lineas as $l)
 									<option value="{{ $l->id }}" >
 										{{ $l->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
 							@error('linea') <span class="text-danger">{{ $message }}</span> @enderror		               
 						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Fabricante</label>
		 					<select wire:model="fabricante"  class="form-control text-center">
 								<option value="Elegir" disabled="">Elegir</option>
 								@foreach($fabricantes as $f)
 									<option value="{{ $f->id }}" >
 										{{ $f->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
 							@error('fabricante') <span class="text-danger">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="row mt-3">                     
						<div class="form-group col-lg-3 col-md-3 col-sm-12">
							<h7 class="text-info">Linea Facturación</h7>
		 					<select wire:model="lineafac"  class="form-control text-center">
 								<option value="Elegir" disabled="">Elegir</option>
 								@foreach($lineasfac as $lf)
 									<option value="{{ $lf->id }}" >
 										{{ $lf->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
 							@error('lineafac') <span class="text-danger">{{ $message }}</span> @enderror		               
 						</div>

						<div class="form-group col-lg-2 col-md-2 col-sm-12">
							<h7 class="text-info">Manejo por</h7>
		                    <select wire:model="manejopor"  class="form-control text-center" > 
								<option value="UNIDAD">UNIDAD</option>
 								<option value="PRESENTACION">PRESENTACION</option>
			            	</select>    
		                </div>
                		<div class="form-group col-lg-4 col-md-4 col-sm-12">
                    		<h7 class="text-info">Barra</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-tags la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="barra" class="form-control"
							 	maxlength="80" placeholder="escanear codigo barra">
	                   		</div>
						</div>

                		<div class="form-group col-lg-3 col-md-3 col-sm-12">
                    		<h7 class="text-info">PrecioVenta</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-tags la-lg"></i>
									</div>
                        		</div>
                        		<input wire:model.lazy="precioventa" type="number"  class="form-control" placeholder="0.0000">
                    		</div>
						</div>							
	            	</div>

					<div class="row mt-3">                     
						<div class="form-group col-lg-3 col-md-3 col-sm-12">
                    		<h7 class="text-info">PrecioCosto</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="number" wire:model.lazy="costo" class="form-control"
							 	value="{{number_format($costo,4)}}"
							 	placeholder="Precio de costo sin iva">
                    		</div>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12">
                    		<h7 class="text-info">UltimaCompra</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-calendar la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="ultimcompra" class="form-control"
							 	value="${{$ultimacompra}}"
							 	placeholder="fecha de última compra">
                    		</div>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12">
                    		<h7 class="text-info">Ultimo Costo</h7>
                    		<div class="input-group mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-calculator la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="ultimocosto" class="form-control"
							 	value="${{number_format($ultimocosto,4)}}"
							 	placeholder="Ultimo Precio costo sin iva">
                    		</div>
						</div>
					</div>
				</div>
				<div class="row justify-content-between">
           			<div class="col-md-4 col-lg-4 col-sm-12">
               			<button wire:click.prevent="CreateOrUpdate()"
		 				class="btn btn-success mt-4">Grabar</button>
            		</div>
      			</div>
			</div>
  		</div>
	</div>
<!-- </section> -->

