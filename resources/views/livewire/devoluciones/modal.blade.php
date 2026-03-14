<div class="modal fade" id="modalLine" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"> Detalle Devolucion de Producto</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled wire:model.defer="mdproducto" id="mdproducto" class="form-control" placeholder="....">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad Despachada</h7>
                                <div class="input-group input-group-sm">
                                    <input type="hidden" wire:model.defer="mddevuelto" id="mddevuelto">
                                    <input type="hidden" wire:model.defer="mdregistro" id="mdregistro">
                                    <input type="text" disabled wire:model.defer="mdcantidad" id="coriginal"  class="form-control" placeholder="cantidad a devolver..">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad a Devolver</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" wire:model.defer="mdadevolver" onkeypress="return CheckNumeric()" 
                                    onkeyup="validarDevolver()" id="cdevolver"  class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" wire:click="saveLine()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>