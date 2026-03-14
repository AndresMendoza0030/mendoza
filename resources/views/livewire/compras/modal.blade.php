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

                        <!--div buscador-->        
                            <div class="row">
               		            <h7 id="lbidproducto"  class="text-info">Buscar por Codigo o Barra</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeyup = "buscarBarra();" id ="abuscar" class="form-control" placeholder="Buscar...">
                                </div>
                            </div>
                        <!--fin buscador-->        

                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="elproducto" class="form-control" placeholder="....">
                                    <input type="hidden" id="idproducto">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Manejo Producto  -  Ultimo Costo</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="manejopor" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Forma Manejo Producto">
                                    $<input type="text" disabled id="ultimocosto" class="form-control" 
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Ultimo Costo">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Cantidad y Costo</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" onkeypress="return CheckNumeric()" onkeyup="mtoProd()" id="cantidad"  class="form-control"
                                    pattern="^[0-9]{1,5}(\.[0-9]{0,4})?$" placeholder="cantidad"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Cantidad de Compra">
                                    $<input type="text" onkeypress="return CheckNumeric()" onkeyup="mtoProd()" id="costounitario"  class="form-control" pattern="^[0-9]{1,5}(\.[0-9]{0,4})?$" placeholder="costo"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Costo Unitario">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Total Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="montoproducto" class="form-control"
                                    pattern="^[0-9]{1,5}(\.[0-9]{0,4})?$" disabled placeholder="total"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Total Producto">
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