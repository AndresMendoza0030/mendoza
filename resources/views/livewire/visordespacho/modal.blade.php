<div class="modal fade" id="modalDespachoItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <input type="hidden" id="idmovimiento">
                                    <input disabled type="text" id="mdproducto"  class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Solicitado</label>
                                    <input disabled type="text" id="mdcantidad"  class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Existencia</label>
                                    <input disabled type="text" id="mdstock"  class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="table-responsive-sm mt-2">
                                <table id="mdtabla"  class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                                    <thead>
                                        <tr>                                                   
                                            <th class="text-center ">Lote Vcto</th>
                                            <th class="text-center ">Cnt.Despacha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mdtbody">

                                    </tbody>
                                </table>
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