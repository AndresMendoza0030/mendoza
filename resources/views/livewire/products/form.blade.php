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
	                        <h5 class="text-center"><b>DATOS DEL PRODUCTO</b></h5>
    	                </div>
        	        </div>  

            		<!--div datos de producto/servicio -->
             		<div class="row mt-3">                     
		                <div class="form-group col-lg-6 col-md-5 col-sm-12">
        		            <h7 class="text-info">Nombre </h7>
                    		<div class="input-group input-group-sm">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  wire:model.lazy="nombre"  class="form-control" maxlength="100"
								style="text-transform:uppercase" onchange="javascript:concambios=true;"  placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
        		            </div>
                		    @error('nombre')
								<span class="text-danger">{{ $message }}</span>
							@enderror  
		                </div>
                		<div class="form-group col-lg-3 col-md-5 col-sm-12">
                    		<h7 class="text-info">Presentación</h7>
                    		<div class="input-group input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="presentacion" class="form-control" maxlength="100"
								style="text-transform:uppercase" onchange="javascript:concambios=true;" placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                    		</div>
                		</div>
                		<div class="form-group col-lg-3 col-md-5 col-sm-12">
                    		<h7 class="text-info">Manejo Por:</h7>
                    		<div class="input-group input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="manejopor" class="form-control" maxlength="100"
								style="text-transform:uppercase" onchange="javascript:concambios=true;" placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                    		</div>
                		</div>
					</div>

					<div class="row mt-3">                     
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Clase Catálogo</label>
							<div class="input-group input-group-sm">
		 					<select onchange="javascript:concambios=true;" wire:model.lazy="clase" class="form-control text-center">
 								<option value="">Elegir</option>
 								@foreach($clases as $c)
 									<option value="{{ $c->id }}" >
 										{{ $c->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
							</div>
 							@error('clase') <span class="text-danger">{{ $message }}</span> @enderror		               
 						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Linea Catálogo</label>
							<div class="input-group input-group-sm">
		 					<select wire:model.lazy="linea"  onchange="javascript:concambios=true;" class="form-control text-center">
 								<option value="" >Elegir</option>
 								@foreach($lineas as $l)
 									<option value="{{ $l->id }}" >
 										{{ $l->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
							</div>
 							@error('linea') <span class="text-danger">{{ $message }}</span> @enderror		               
 						</div>

						<div class="form-group col-lg-4 col-md-4 col-sm-12">
		 					<label >Fabricante</label>
							 <div class="input-group input-group-sm">
		 					<select wire:model.lazy="fabricante"  onchange="javascript:concambios=true;" class="form-control text-center">
 								<option value="">Elegir</option>
 								@foreach($fabricantes as $f)
 									<option value="{{ $f->id }}" >
 										{{ $f->nombre}}
 									</option>                                       
		 						@endforeach                              
 							</select>	
							</div>
 							@error('fabricante') <span class="text-danger">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="row mt-3">                     
                		<div class="form-group col-lg-3 col-md-4 col-sm-12">
                    		<h7 class="text-info">Barra</h7>
                    		<div class="input-group  input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-tags la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.lazy="barra" onchange="javascript:concambios=true;" class="form-control"
							 	maxlength="80" placeholder="escanear codigo barra">
	                   		</div>
						</div>

                		<div class="form-group col-lg-2 col-md-3 col-sm-12">
                    		<h7 class="text-info">Registro</h7>
                    		<div class="input-group  input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-tags la-lg"></i>
									</div>
                        		</div>
                        		<input wire:model.defer="registrosanitario" type="text"  onchange="javascript:concambios=true;" class="form-control" placeholder="Registro Sanitario">
                    		</div>
						</div>							
						<div class="form-group col-lg-2 col-md-3 col-sm-12">
                    		<h7 class="text-info">CostoPromedio</h7>
                    		<div class="input-group input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.defer="costo" onkeypress="return CheckNumeric()"
								 onchange="javascript:concambios=true;" class="numero4 form-control" placeholder="0.0000">
                    		</div>
						</div>
						<div class="form-group col-lg-2 col-md-3 col-sm-12">
                    		<h7 class="text-info">UltimaCompra</h7>
                    		<div class="input-group  input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-calendar la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.defer="ultimacompra" onchange="javascript:concambios=true;" class="form-control"
							 	value="${{$ultimacompra}}"
							 	placeholder="fecha de última compra">
                    		</div>
						</div>
						<div class="form-group col-lg-2 col-md-3 col-sm-12">
                    		<h7 class="text-info">Ultimo Costo</h7>
                    		<div class="input-group  input-group-sm">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-calculator la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" wire:model.defer="ultimocosto" onkeypress="return CheckNumeric()"
								onchange="javascript:concambios=true;" class="numero4 form-control"
							 	placeholder="0.0000">
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

<script type="text/javascript">
	$(document).ready(function(){
  		$('.numero4').mask('999,999,999.9999', {reverse: true});
		
	});

</script>
