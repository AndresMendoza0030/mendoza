
<div class="row layout-top-spacing" >
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h6><b>Movimiento de: {{$idproducto}} - {{$elproducto}}</b></h6>
                    </div> 
                </div>
            </div>

            <div class="row mb-3 mt-2">
                <div class="col-lg-2 col-md-12 col-sm-12">
                    <button class="btn btn-dark" wire:click="regresaMaster()">
                        <i class="la la-chevron-left"></i>
                    </button>
                </div>

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGrouptipomovimiento">Bodega</label>
                        </div>
                        <select class="form-control" wire:model="mibodega" id="bodega">
                            <option value="">Elegir</option>
 	    				    @foreach($bodegas as $bd)
 							    <option value="{{ $bd->id}}">
 						            {{$bd->nombre}}
 							    </option>                                       
						    @endforeach                              
                        </select>
                    </div>
                </div>
	            <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                        </div>
                        <input type="date" aria-label="Desde" class="form-control"  wire:model="desde" >
                        <input type="date" aria-label="Hasta" class="form-control"  wire:model="hasta" >
                    </div>
                </div>
                <div class="col-lg-2 col-md-12 col-sm-12">
                    <button class="btn btn-dark" wire:click="mostrarMovimiento()">
                        Mostrar<i class="la la-play"></i>
                    </button>
                </div>


            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>                                                   
                            <th class="col-1" style="font-size: .60rem;">Fecha</th>
                            <th class="col-1" style="font-size: .60rem;">Documento</th>
                            <th class="col-2" style="font-size: .60rem;">CONCEPTO</th>                            
                            <th class="col-1" style="font-size: .60rem;">Anterior</th>
                            <th class="col-1" style="font-size: .60rem;">Cantidad</th>
                            <th class="col-1" style="font-size: .60rem;">Posterior</th>
                            <th class="col-1" style="font-size: .60rem;">CostoUnit S/iva</th>
                            <th class="col-1" style="font-size: .60rem;">MtoCosto S/iva</th>
                            <th class="col-1" style="font-size: .60rem;">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimi as $m)
                            <tr>
                                <td class="text-left" style="font-size: .60rem;">{{date('d-m-Y', strtotime($m->fecha))}}</td>
                                <td class="text-center" style="font-size: .60rem;">
                                    @if($m->paciente_id>0)
                                        Pac No. {{$m->paciente_id}} / {{$m->tipodoc}} - {{$m->numerodoc}}
                                    @else
                                        {{$m->tipodoc}} - {{$m->numerodoc}}                                    
                                    @endif
                                </td>
                                <td class="text-left" style="font-size: .60rem;">{{$m->concepto}}</td>
                                <td class="text-center" style="font-size: .60rem;">{{number_format($m->anterior,0)}}</td>
                                <td class="text-center" style="font-size: .60rem;">{{number_format($m->cantidad,0)}}</td>
                                <td class="text-center" style="font-size: .60rem;">{{number_format($m->posterior,0)}}</td>
                                <td class="text-right" style="font-size: .60rem;">{{number_format(round($m->precosto,4),4)}}</td>                                
                                <td class="text-right" style="font-size: .60rem;">{{number_format(round($m->gravado,4),4)}}</td>
                                <td class="text-left" style="font-size: .60rem;"> {{$m->nusuario}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div> 
    </div>
<div>

@livewireScripts

<script type="text/javascript">
$(document).ready(function(){
    $('.numero4').mask('999,999,999.9999', {reverse: true});
    
});

</script>
