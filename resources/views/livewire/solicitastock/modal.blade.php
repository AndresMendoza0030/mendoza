<div class="modal fade" id="modalCompraProd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detalle Compra de Producto</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdproducto" class="form-control" placeholder="....">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Manejo Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="manejopor" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Forma Manejo Producto">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Existencia</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdstock"  class="form-control"
                                    placeholder="stock actual"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Existencia Actual">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeypress="return CheckNumeric()" onkeyup="validarStock()" id="mdcantidad"  class="form-control"
                                    pattern="^[0-9]{1,5}(\.[0-9]{0,4})?$" placeholder="cantidad"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Cantidad de Compra">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveLine()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>