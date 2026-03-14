<div class="modal fade" id="modalEdicion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detalle Despacho de Producto</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Nombre del Producto</label>
                                    <input  type="text" id="mdproducto" disabled class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Solicitado</label>
                                    <input  type="text" id="mdcantidad" disabled class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Existencia</label>
                                    <input disabled type="text" id="mdstock" disabled   class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Autorizado</label>
                                    <input  type="text" id="mdautorizado" onkeypress="return CheckNumeric()" class="form-control form-control-sm">
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveItem()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>