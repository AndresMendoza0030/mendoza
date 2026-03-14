@if($action == 3)
<div class="modal fade" id="modalExiste" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-center">Stock: {{$elproducto}} </h6>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-3">
                                <thead>
                                    <tr>                                                   
                                        <th class="col-7">Bodega</th>
                                        <th class="col-3">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existe as $sk)
                                        <tr>
                                            <td class="text-left"><p class="mb-0">{{$sk->labodega}}</p></td>
                                            <td class="text-right">{{number_format($sk->existencia,2,'.',',')}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>                                                   
                                        <th class="text-right">Total</th>
                                        <th class="text-right">{{number_format($totalexiste,2,'.',',')}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" onclick="cerrarModal()" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Regresar</button>
            </div>
        </div>
    </div>
</div>
@endif  

@livewireScripts

<script type="text/javascript">
</script>
