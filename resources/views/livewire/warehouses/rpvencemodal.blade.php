<div class="modal fade" id="rpvencemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vencimientos Hasta:</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-8 col-md-12 col-sm-12">
                                    <label >Mes Vencimiento</label>
                                    <select id="selevencemes" class="form-control text-center">
                                        <option value="Elegir" disabled="">Elegir Mes</option>
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
                                </div>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label>Año</label>
                                    <input type="text" wire:model.defer="annovence" class="form-control" placeholder="año vence">
                                    <input type="hidden" id="bodegavence" wire:model.defer="bodegavence" >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="generaRepoVence()" class="btn btn-primary saveClass">Imprimir</button>
            </div>
        </div>
    </div>
</div>