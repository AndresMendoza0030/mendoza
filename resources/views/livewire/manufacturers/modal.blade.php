<div class="modal fade" id="modalClass" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descripción del Fabricante</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Nombre</label>
                                    <input type="text" style="text-transform:uppercase" id="nombre" class="form-control" placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveManufacturer()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>