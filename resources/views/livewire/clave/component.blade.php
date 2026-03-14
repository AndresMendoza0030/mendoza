<div class="row layout-top-spacing">    
    <div class="col-xl-6 col-lg-6 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Cambio de Clave Acceso</b></h5>
                    </div> 
                </div>
            </div>
            <div class="widget-one">
               <div class="card mb-1">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    		<h7 class="text-info">*Clave Actual</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-key la-lg"></i>
									</div>
                        		</div>
                        		<input type="password"  wire:model.lazy="actual" class="form-control"
								placeholder="Ingrese clave actual. . . ">
                    		</div>
                		</div>
				    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    		<h7 class="text-info">*Nueva Contraseña</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-key la-lg"></i>
									</div>
                        		</div>
                        		<input type="password"  wire:model.lazy="nueva" class="form-control"
								placeholder="Ingrese nueva clave. . . ">
                    		</div>
                		</div>
				    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    		<h7 class="text-info">*Confirmar Clave</h7>
                    		<div class="input-group input-group-sm mb-2 mr-sm-2">
                        		<div class="input-group-prepend">
                            		<div class="input-group-text">
										<i class="la la-key la-lg"></i>
									</div>
                        		</div>
                        		<input type="password"  wire:model.lazy="confirma" class="form-control"
								placeholder="Confirme nueva clave . . . ">
                    		</div>
                		</div>
				    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" wire:click="salvaNuevaClave()" >Confirmar</button>
        </div>
    </div>
</div>

@livewireScripts
<script type="text/javascript">
   document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('msg-ok', dataMsg =>{ $('#modalClass').modal('hide')})
   })
   
</script>