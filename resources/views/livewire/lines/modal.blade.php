<div class="modal fade" id="modalLine" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descripción de Línea</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Nombre</label>
                                    <input type="text" style="text-transform:uppercase" id="nombre" class="form-control" placeholder="...." onkeyup="javascript:this.value=this.value.toUpperCase();">
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Cuenta Contable</label>
                                    <input type="text"  id="cuenta" class="form-control" pattern="[0-9]+" placeholder="0000000">
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label>Clase de Inventario</label>
                                    <select  id="clase" class="form-control text-center">
                                        <option value="Seleccionar" selected>Seleccionar</option>
                                        @foreach($clases as $cl)
                                            <option value="{{$cl->id}}" >
                                                 {{$cl->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
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