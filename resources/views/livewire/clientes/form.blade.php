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
	                        <h5 class="text-center"><b>DATOS DEL CLIENTE</b></h5>
    	                </div>
        	        </div>  

            		<!--div datos de producto/servicio -->
             		<div class="row mt-3">                     
		                <div class="form-group col-lg-4 col-md-12 col-sm-12">
        		            <h7 class="text-info">Razon </h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  id="razon" wire:model.lazy="razon" class="form-control" maxlength="100"
								style="text-transform:uppercase"  placeholder="...." onblur=mascaraOn() onkeyup="javascript:this.value=this.value.toUpperCase();">
        		            </div>
		                </div>
		                <div class="form-group col-lg-4 col-md-12 col-sm-12">
        		            <h7 class="text-info">Cuenta</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  id="cuenta" wire:model.lazy="cuentacontable" class="form-control" maxlength="100"
								onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" placeholder="Ingrese Cuenta Contable" >
        		            </div>
		                </div>

						<div class="form-group col-lg-4 col-md-4 col-sm-12">
        		            <h7 class="text-info">Email* </h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-envelope la-lg"></i>
									</div>
                        		</div>
		                        <input type="email"  id="email" wire:model.lazy="email" class="form-control" maxlength="100"
								 placeholder="cliente@info...." >
        		            </div>
		                </div>

					</div>
					<div class="row">                     
		                <div class="form-group col-lg-2 col-md-4 col-sm-12">
        		            <h7 class="text-info">Nit </h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text" wire:model.lazy="nit"  id="nit"  class="nit form-control">
        		            </div>
		                </div>
		                <div class="form-group col-lg-3 col-md-4 col-sm-12">
        		            <h7 class="text-info">DUI</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
								<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  id="dui" wire:model.lazy="dui"  class="dui form-control">
        		            </div>
		                </div>
                		<div class="form-group col-lg-3 col-md-4 col-sm-12">
                    		<h7 class="text-info">NRC</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" id="nrc" wire:model.lazy="nrc" class="form-control" maxlength="100"
								onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" placeholder="...." >
                    		</div>
                		</div>
                		<div class="form-group col-lg-3 col-md-12 col-sm-12">
                    		<h7 class="text-info">Giro</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" id="giro" wire:model.lazy="giro" class="form-control" maxlength="100"
								placeholder="...." style="text-transform:uppercase" >
                    		</div>
                		</div>
					</div>
					<div class="row">                     
                		<div class="form-group col-lg-4 col-md-4 col-sm-12">
                    		<h7 class="text-info">Dirección</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-map-marker la-lg"></i>
									</div>
                        		</div>
                        		<input type="text" id="direccion" wire:model.lazy="direccion" class="form-control" maxlength="100"
								style="text-transform:uppercase"  placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                    		</div>
                		</div>
		                <div class="form-group col-lg-3 col-md-4 col-sm-12">
        		            <h7 class="text-info">TELEFONO </h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-phone la-lg "></i>
									</div>
                        		</div>
		                        <input type="text"  id="telefono" wire:model.lazy="telefono"  class="movil form-control" maxlength="20"
								placeholder="0000-000000-000-0">
        		            </div>
		                </div>
		                <div class="form-group col-lg-3 col-md-4 col-sm-12">
        		            <h7 class="text-info">CONTACTO</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg "></i>
									</div>
                        		</div>
		                        <input type="text"  id="contacto" wire:model.lazy="contacto"  class="form-control" maxlength="20"
								placeholder="........" style="text-transform:uppercase">
        		            </div>
		                </div>
		                <div class="form-group col-lg-2 col-md-4 col-sm-12">
        		            <h7 class="text-info">Crédito</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
			                    <select id="credito"  onchange="javascript:concambios=true;"  wire:model.lazy="credito" class="form-control text-center" > 
									<option value="NO">NO</option>
 									<option value="SI">SI</option>
			        	    	</select>    
        		            </div>
						</div>

					</div>
					<div class="row">                     
						<div class="row justify-content-between">
        		   			<div class="col-md-4 col-lg-4 col-sm-12">
               					<button wire:click="grabarDatos()"
		 						class="btn btn-success mt-4">Grabar</button>
            				</div>
		      			</div>
					</div>
				</div>
  			</div>
		</div>
	</div>
<!-- </section> -->
<script>

		$('.js-example-basic-single').select2();


</script>