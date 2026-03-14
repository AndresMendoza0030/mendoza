<div class="modal fade" id="modalServicio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detalle Cargo por Servicio</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdproducto" wire:model.defer="mdproducto" class="form-control" placeholder="....">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeypress="return CheckNumeric()" id="mdcantidad"  class="form-control"
                                    wire:model.defer="mdcantidad" onchange="validaMonto()">
                                </div>

                                <h7 class="text-info fs-6 mt-2">P.Unitario</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeypress="return CheckNumeric()" id="mdpreciog"  class="form-control"
                                    wire:model.defer="mdpreciog" disabled onchange="validaMonto()">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Monto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled onkeypress="return CheckNumeric()" id="mdmonto"  class="form-control"
                                    wire:model.defer="mdmonto">
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="updLine()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>