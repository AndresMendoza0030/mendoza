<div class="modal fade" id="modaldatosdetalle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">DETALLE DE ESTUDIO DEL PERFIL: {{$}}</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Nombre del Estudio</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdEstudio"  wire:model.defer="mdEstudio" class="form-control" >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-4 col-sm-12">
                                    <h7 class="text-info" style="font-size: .80rem;" >Precio Venta</h7>
                                    <div class="input-group input-group-sm mr-sm-2">
                                    <input type="text" id="precio" wire:model.def="mdprecio" onkeypress="return CheckNumeric()" style="font-size: .70rem;" class="form-control text-right">
                                </div>
                                <div class="col-lg-6 col-md-4 col-sm-12">
                                    <h7 class="text-info" style="font-size: .80rem;" >% Descuento</h7>
                                    <div class="input-group input-group-sm mr-sm-2">
                                        <input type="text" id="descuento" wire:model.def="mddescuento" onkeypress="return CheckNumeric()" style="font-size: .70rem;" class="form-control text-right">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark"  onclick="updModo()" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Regresar</button>
                <button type="button" onclick="updValores()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>