<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Visor de Existencias x Bodega:{{$cbodega}}</b></h5>
                    </div> 
                </div>
            </div>

            <!-- Buscador -->
                <div class="row justify-content-between mb-1">
                    <div class="col-lg-1 col-md-4 col-sm-12">
                        <button class="btn btn-dark" onclick="doAction(1)">
                            <i class="la la-chevron-left"></i>
                        </button>
                    </div>

	                <div class="col-lg-4 col-md-4 col-sm-12">
		                <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input type="text" wire:model="search" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            <!-- Buscador -->

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                    <thead>
                        <tr>                                                   
                            <th class="text-center">CLASE</th>
                            <th class="text-center">DESCRIPCIÓN PRODUCTO</th>
                            <th class="text-center">EXISTENCIA</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($existe as $ex)
                            <tr>
                                <td class="text-left">{{$ex->nombreclase}}</td>
                                <td class="text-left">{{$ex->producto_id}} - {{$ex->nombreproducto}}</td>
                                <td class="text-center">{{$ex->existencia}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                    	<li>
                                        <a href="javascript:void(0);"          		
		                                    onclick=""
		                                    data-toggle="tooltip" data-placement="top" title="Movimiento de Producto"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>

	                                    </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$existe->links()}}

            </div>
        </div> 
    </div>
</div>