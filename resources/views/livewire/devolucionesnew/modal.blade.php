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
                                    <input type="text" disabled id="mdproducto" class="form-control" placeholder="....">

                                    <input type="hidden" id="hdocumento">
                                    <input type="hidden" id="hbodega">
                                    <input type="hidden" id="hidproducto">
                                    <input type="hidden" id="hdevuelto">
                                    <input type="hidden" id="hproducto">
                                    <input type="hidden" id="hunitario">                                    
                                    <input type="hidden" id="hmonto">
                                    <input type="hidden" id="hunico">
                                    <input type="hidden" id="hlotevence">                                   
                                    <input type="hidden" id="hclotevence">
                                    <input type="hidden" id="hidpaciente">

                                </div>
                                <h7 class="text-info fs-6 mt-2">Manejo Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="manejopor" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Forma Manejo Producto">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad Original</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="coriginal"  class="form-control" placeholder="cantidad a devolver..">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad a Devolver</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeypress="return CheckNumeric()" onkeyup="validarDevolver()"
                                     id="cdevolver"  class="form-control">
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