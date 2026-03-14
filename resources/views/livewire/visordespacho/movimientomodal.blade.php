<div class="modal fade" id="cargomodal" style="overflow:hidden;" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Generar Cargo a Hoja de Cumplimiento</h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Nombre del Producto</label>
                                    <input type="hidden" id="idmovimiento">
                                    <input disabled type="text" id="mdproducto" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Solicitado</label>
                                    <input disabled type="text" id="mdcantidad" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Existencia</label>
                                    <input disabled type="text" id="mdstock" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="form-group col-lg-8 col-md-12 col-sm-12">
                                <h7 class="text-info">Bodega Operativa</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                <select wire:model.defer="bodegapred"  disabled id="bodega" class="form-control text-center">
                                    <option value="">Elegir</option>
                                    @foreach($bodegas as $bd)
                                        <option value="{{ $bd->id }}" >
                                            {{ $bd->nombre}}
                                        </option>                                       
                                    @endforeach                              
                                </select>	
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <h7 class="text-info">Seleccione Producto</h7>
                                <div wire:ignore class="input-group input-group-sm mb-2  mr-sm-2">
                                <select wire:model="producto"  id="selectproducto" onchange="mostrarDatos()" class="js-example-basic-single form-control">
                                    <option value="">Elegir</option>
                                    @foreach($datos as $pr)
                                        <option value="{{$pr->id}}_{{$pr->nombreproducto}}_{{$pr->precioventa}}_{{$pr->tipo}}">
                                        {{$pr->barra}} {{$pr->nombre}}
                                        </option>                                       
                                    @endforeach                              
                                </select>	
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                <h7 class="text-info">Stock</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="text" disabled value="" id="stock" class="form-control">
                                </div>
                            </div>

                            <div  class="form-group col-lg-6 col-md-12 col-sm-12">
                                <h7 class="text-info">Cantidad</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="number" onChange="validarStock()" value="" id="cantidad" class="form-control">
                                </div>
                            </div>
                            <div  class="form-group col-lg-6 col-md-12 col-sm-12">
                                <h7 class="text-info">Precio</h7>
                                <div class="input-group input-group-sm mb-2 mr-sm-2">
                                    <input type="text" disabled value="" id="unitario" class="form-control">
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
@livewireScripts
<script type="text/javascript">
</script>