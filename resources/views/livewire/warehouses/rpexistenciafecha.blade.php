<div class="modal fade" id="rpexistenciafecha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Existecias Hasta:</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-8 col-md-12 col-sm-12 text-center">
                                    <label>Fecha:</label>
                                    <input id="mdfecharepo" type="date" wire:model.defer="fecharepo" class="form-control" >
                                    <input type="hidden" id="bodegaex" >
                                    <input type="hidden" id="modorepo" >                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="generaExistenciaFecha()" class="btn btn-primary saveClass">Imprimir</button>
            </div>
        </div>
    </div>
</div>