<div class="modal fade" id="modaledita" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detalle Conteo x Lote Vence</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdproducto" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Nombre del Producto">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Lote Vencimiento</h7>
                                <div class="input-group input-group-sm">
                                    <input type="hidden"  id="mdloteid">
                                    <input type="hidden"  id="mdcantiante">
                                    <input type="text" disabled id="mdlote" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Lote Vencimiento">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Conteo Lote</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="mdcantilote"  class="form-control"
                                    placeholder="stock del lote"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Conteo x lote">
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="updVence()" class="btn btn-primary saveClass">Actualizar</button>
            </div>
        </div>
    </div>
</div>