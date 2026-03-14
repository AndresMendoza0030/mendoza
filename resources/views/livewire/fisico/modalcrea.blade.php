<div class="modal fade" id="modalcrea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Ingresar Conteo x Lote Vence</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Producto</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdcproducto" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Nombre del Producto">
                                </div>

                                <h7 class="text-info fs-6 mt-2">Mes y AñoVencimiento</h7>
                                <div class="input-group input-group-sm">
                                    <input type="hidden"  id="mdcloteid">
                                    <input type="hidden"  id="mdccantiante">
                                    <select id="mdmesv"   class="form-control">
                                        <option value="">Elegir</option>
                                        <option value="1">ENERO</option>
                                        <option value="2">FEBRERO</option>
                                        <option value="3">MARZO</option>
                                        <option value="4">ABRIL</option>
                                        <option value="5">MAYO</option>
                                        <option value="6">JUNIO</option>
                                        <option value="7">JULIO</option>
                                        <option value="8">AGOSTO</option>
                                        <option value="9">SEPTIEMBRE</option>                                                                                                                                                                                                                        
                                        <option value="10">OCTUBRE</option>
                                        <option value="11">NOVIEMBRE</option>
                                        <option value="12">DICIEMBRE</option>                                                                        
                                    </select>
                                    <input type="text"  id="mdannov" class="form-control"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Año Vencimiento">
                                </div>


                                <h7 class="text-info fs-6 mt-2">Conteo Lote</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="mdccantilote"  class="form-control"
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
                <button type="button" onclick="grabarVence()" class="btn btn-primary saveClass">Crear</button>
            </div>
        </div>
    </div>
</div>