<div class="modal fade" id="datosestudiomodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">NOMBRE COMERCIAL DEL PERFIL</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <h7 class="text-info fs-6">Nombre del Perfil</h7>
                                <div class="input-group input-group-sm">
                                    <input type="text" disabled id="mdperfil"  wire:model.defer="mdperfil" class="form-control" >
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