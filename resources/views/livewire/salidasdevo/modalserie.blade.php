<div class="modal fade " id="modalserie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Grabar Serie para : {{$sujeto}}</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <h7 class="text-info">Serie </h7>
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <div  class="input-group-text">
                                            <i class="la la-edit la-lg"></i>
                                        </div>
                                    </div>
                                    <input type="text"  id="lserie" wire:model.lazy="laserie" class="form-control"
                                    style="text-transform:uppercase"  placeholder="Ingrese Serie Fiscal del Proveedor"
                                    onkeyup="javascript:this.value=this.value.toUpperCase();">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveSerie()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>
