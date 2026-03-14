<div class="modal fade" id="modalLinea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información de Paciente</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Clase Facturación</label>
                                    <input type="text" disabled id="lnombre" class="form-control" >
                                </div>
                            </div>
                            <div class="row">                            
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>Sub- Total</label>
                                    <input type="text" disabled id="lsubtotal" class="form-control" >
                                </div>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>% Descuento</label>
                                    <input type="text" id="lpdescuento" placeholder="ingrese %"
                                    onkeypress="return CheckNumeric()" onkeyup="aplicaDescuento()" class="form-control" >
                                </div>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>V.Descuento</label>
                                    <input type="text" disabled id="ldescuento" class="form-control" >
                                </div>
                            </div>
                            <div class="row">                            
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>V.Gravado</label>
                                    <input type="text" disabled id="lgravado" class="form-control" >
                                </div>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>V. IVA</label>
                                    <input type="text" disabled id="liva" class="form-control" >
                                </div>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>Total Clase </label>
                                    <input type="text" disabled id="ltotal" class="form-control" >
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveDescuento(llinea)" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>