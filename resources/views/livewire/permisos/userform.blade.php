<!--<section id="datosRegistro">  -->
    <div class="row layout-top-spacing" >
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                    <!--div titulo y boton regersar -->
                    <div class="row">
                    	<div class="col-2">
                        	<button class="btn btn-dark" wire:click="doAction(1)" style="font-size: .70rem;">
                            	<i class="la la-chevron-left"></i>
                        	</button>
                    	</div>
                    	<div class="col-8">
	                        <h5 class="text-center" style="font-size: .70rem;"><b>DATOS DEL USUARIO</b></h5>
    	                </div>
        	        </div>  

            		<!--div datos de producto/servicio -->
             		<div class="row mt-1">                     
		                <div class="form-group col-lg-3 col-md-12 col-sm-12">
        		            <h7 class="text-info" style="font-size: .70rem;">Nombre de Usuario </h7>
                    		<div class="input-group input-group-sm  mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-edit la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  id="nombreusuario" wire:model.lazy="nameuser" class="form-control" maxlength="100" style="font-size: .70rem;"
								style="text-transform:uppercase"  placeholder="Nombre Completo Usuario " onkeyup="javascript:this.value=this.value.toUpperCase();">
        		            </div>
		                </div>
		                <div class="form-group col-lg-3 col-md-4 col-sm-12">
        		            <h7 class="text-info" style="font-size: .70rem;">User Login* </h7>
                    		<div class="input-group input-group-sm  mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div  class="input-group-text">
										<i class="la la-user la-lg"></i>
									</div>
                        		</div>
		                        <input type="text"  id="userlogin" wire:model.lazy="loginuser" class="form-control" style="font-size: .70rem;" maxlength="50"
								 placeholder="usuario para sistema...." >
        		            </div>
		                </div>
						<div class="form-group col-lg-3 col-md-4 col-sm-12">
							<h7 class="text-info" style="font-size: .70rem;">Bodega Pred</h7>
							<div class="input-group input-group-sm  mr-sm-2">
							<select wire:model.lazy="bodegapred"  class="form-control text-center" style="font-size: .70rem;">
								<option value="">Elegir</option>
								@foreach($bodegas as $b)
									<option value="{{ $b->id }}" >
										{{ $b->nombre}}
									</option>                                       
								@endforeach                              
							</select>	
							</div>
						</div>
						<div class="form-group col-lg-3 col-md-4 col-sm-12">
							<h7 class="text-info" style="font-size: .70rem;">Destino Pred</h7>
							<div class="input-group input-group-sm mr-sm-2">
							<select wire:model.lazy="destinopred" class="form-control text-center" style="font-size: .70rem;">
								<option value="">Elegir</option>
								@foreach($destinos as $d)
									<option value="{{ $d->id }}" >
										{{ $d->nombre}}
									</option>                                       
								@endforeach                              
							</select>	
							</div>
						</div>
					</div>
					<div class="row">                     
						<div class="row justify-content-between">
        		   			<div class="col-md-4 col-lg-4 col-sm-12">
               					<button wire:click="grabaUser()"
		 						class="btn btn-sm btn-success mt-1 ml-3" style="font-size: .70rem;">Grabar</button>
            				</div>
		      			</div>
					</div>
				</div>
  			</div>
		</div>
	</div>
<!-- </section> -->
<script>

</script>