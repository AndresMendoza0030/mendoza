<div class="modal fade " id="modalproveedor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Datos de Proveedor</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <h7 class="text-info">Razon* </h7>
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <div  class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text"  id="mrazon" wire:model.defer="mrazon" class="form-control"
                                    style="text-transform:uppercase"  placeholder="Ingrese razón según documento..." onkeyup="javascript:this.value=this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-4 col-sm-12">
                                <h7 class="text-info">Cta.Contable</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div  class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text"  id="mdui" wire:model.defer="mcuentaco" placeholder="Ingrese Cta Contable..." class="form-control">
                                </div>
    		                </div>
                            <div class="form-group col-lg-6 col-md-4 col-sm-12">
                                <h7 class="text-info">DUI</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div  class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text"  id="mdui" wire:model.defer="mdui" placeholder="Ingrese DUI (no contribuyentes)..." class="dui form-control">
                                </div>
    		                </div>
                            <div class="form-group col-lg-6 col-md-4 col-sm-12">
                                <h7 class="text-info">Nit </h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div  class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text" wire:model.defer="mnit"  id="mnit"  class="nit form-control">
                                </div>
    		                </div>

                            <div class="form-group col-lg-6 col-md-4 col-sm-12">
                                <h7 class="text-info">NRC</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text" id="mnrc" wire:model.defer="mnrc" class="form-control"  placeholder="Ingrese No.Registro IVA...." >
                                </div>
                    		</div>
                            <div class="form-group col-lg-10 col-md-12 col-sm-12">
                                <h7 class="text-info">Giro</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text" id="mgiro" wire:model.defer="mgiro" class="form-control" placeholder="Ingrese Actividad Ecónomica. . . . " style="text-transform:uppercase" >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveProveedor()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>
