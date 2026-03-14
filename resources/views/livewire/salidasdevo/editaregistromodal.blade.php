<div class="modal fade" id="editaregistromodal" style="overflow:hidden;" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Datos de Registro de Partida Contable</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <h7 class="text-info">Seleccione Cuenta Contable</h7>
                                <div wire:ignore class="input-group input-group-sm mb-2  mr-sm-2">
                                <select wire:model.defer="lacuentacc"  id="selectcuenta" onchange="mostrarDatos()" class="js-example-basic-single form-control">
                                    <option value="">Elegir</option>
                                    @foreach($cuentascc as $c)
                                        <option value="{{$c->cuenta}}">
                                        {{$c->cuenta}} {{$c->concepto}}
                                        </option>                                       
                                    @endforeach                              
                                </select>	
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                <h7 class="text-info">Cargo</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="text" wire:model.defer="rgcargo"  id="rgcargo" onkeypress="return CheckNumeric()" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                <h7 class="text-info">Abono</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="text" wire:model.defer="rgabono" onkeypress="return CheckNumeric()" id="rgabono" class="form-control">
                                </div>
                            </div>


                            <div  class="form-group col-lg-12 col-md-12 col-sm-12">
                                <h7 class="text-info">Concepto</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="text"  wire:model.defer="rgconcepto" id="rgconcepto" class="form-control">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                <button type="button" onclick="saveRegPda()" class="btn btn-primary saveClass">Grabar</button>
            </div>
        </div>
    </div>
</div>
@livewireScripts
<script type="text/javascript">
</script>