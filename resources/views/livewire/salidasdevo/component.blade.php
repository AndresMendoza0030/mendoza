<div class="row layout-top-spacing">    
    <div class="col-xl-7 col-lg-7 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4" id="inventario">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5 style="font-size: .70rem;"><b>Devoluciones/Vencimientos de Producto</b></h5>
                    </div> 
                </div>
            </div>
       		<!--div datos head documento -->
            <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false" id="inventarioDiv1">                     
                <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
               		<h7 class="text-info" style="font-size: .70rem;">Fecha Movimiento</h7>
               		<div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                   		<div class="input-group-prepend" style="font-size: .70rem;"> 
                       		<div class="input-group-text" style="font-size: .70rem;">
								<i class="la la-calendar la-lg"></i>
							</div>
                   		</div>
                   		<input type="date" wire:model.defer="lafecha" id="lafecha" style="font-size: .70rem;" class="form-control" tabindex=1>
                   	</div>
               	</div>
                <!--div buscador Cliente-->        
                <div class="form-group col-lg-8 col-md-4 " style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Buscador de Proveedor</h7>
                    <div class="input-group input-group-sm mr-sm-2" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <a href="javascript:void(0);" wire:click=iniciarProveedor() data-toggle="tooltip" data-placement="top" title="Nuevo Proveedor" style="font-size: .70rem;">    
                                <i class="la la-file la-lg" ></i></a>
                            </div>
                        </div>
                        <input type="hidden" wire:model.lazy="sujeto_id" id="idprovee">
                        <input type="text" tabindex=2 class="form-control" placeholder="Buscar Proveedor..."
                            onkeyup="javascript:this.value=this.value.toUpperCase();" style="font-size: .70rem;"
                        wire:model="lsujeto" id="proveedor" @focus="isOpen = true" @keydown.escape.window="isOpen = false" 
                        @keydown.shift.tab="isOpen = false" @keydown.tab="isOpen = false">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <a href="javascript:void(0);" onclick="limpiaProvee()"  data-toggle="tooltip" data-placement="top" title="Limpiar Proveedor" style="font-size: .70rem;">
                                <i class="la la-trash la-lg" ></i></a> 
                            </div>
                        </div>
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lsujeto !='')
                            @foreach($proveedores as $r)
                                <li wire:click="mostrarProveedor('{{$r->id}}')" class="list-group-item list-group-item-action">                               
                                    <b>Proveedor: {{$r->razon}}  </b> -<h7 class="text-success">NIT</h7>:{{$r->nit}} - 
                                    <h7 class="text-secondary">NRC</h7>:{{$r->nrc}}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <!--fin buscador Cliente-->
			</div>
            @if($sujetoid>0)
            <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false" style="font-size: .70rem;" id="inventarioDiv2">
                <div wire:ignore class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Bodega</h7>
                    <div class="input-group input-group-sm " style="font-size: .70rem;">
    					<select tabindex=2 wire:model.lazy="bodega"  id="bodega" onchange="asgBodega()" class="form-control text-center" style="font-size: .70rem;" tabindex=3>
 	    				<option value="" style="font-size: .70rem;">Elegir</option>
                        @foreach($bodegas as $bd)
                                <option value="{{ $bd->id }}" style="font-size: .70rem;">
                                    {{ $bd->nombre}}
                                </option>                                       
                        @endforeach                              
                        </select>	
                    </div>
				</div>
                @if($bodega>0)
                <!--div buscador producto-->        
                <div class="form-group col-lg-8 col-md-12 "  id="buscaPro1">
                    <h7 class="text-info" style="font-size: .70rem;">Buscador de Producto</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  tabindex=4 class="form-control" id="buscadorproducto"  placeholder="Buscar Producto..." style="font-size: .70rem;"
                        onkeyup="javascript:this.value=this.value.toUpperCase();" 
                        wire:model="lproducto" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                        @keydown.shift.tab="isOpen = false">
                    </div>
                    <ul class="list-group" x-show.transition.opacity="isOpen">
                        @if($lproducto !='')
                            @foreach($productos as $r)
                                <li wire:click="seleProducto('{{$r}}')" class="list-group-item list-group-item-action">                               
                                    <b>{{$r->producto}}</b> - <h7 class="text-secondary">ID</h7>:{{$r->id}}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                @endif
            </div>
            @if($productoid>0)
            <div class="row" >
                <!--fin buscador producto-->
                <div   id ="bt00" class="form-group col-lg-3 col-md-6 " style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Stock</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <div class="input-group-prepend" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  disabled class="form-control"  value='{{$lstock}}' style="font-size: .70rem;" tabindex=5>
                    </div>
                </div>

                <div  id ="bt3" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .70rem;">   
                    <h7 class="text-info" style="font-size: .70rem;">Cantidad</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input type="text"   id="cantidad" wire:model.defer="lcantidad" style="font-size: .70rem;"
                        onchange=valCantidad() onblur=valCantidad() tabindex=7
                        class="form-control" placeholder="cantidad">
                    </div>
                </div>





                
                @if($lotesprodsum >= 1)
                <div  id ="bt1" class="form-grou col-lg-3 col-md-6 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Lote Vence</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <select  wire:model.defer="loteid" id="lote"  wire:change="asgLote($event.target.value)" class="form-control text-center" style="font-size: .70rem;" tabindex=10>
                            @if($lotesprodsum>1)
                                <option value="" style="font-size: .70rem;">Seleccionar Lote</option>
                                <option value="" style="font-size: .70rem;">Sin Lote</option>                                
                            @endif
                            @if($lotesprodsum==1)
                                <option value="" style="font-size: .70rem;">Sin Lote</option>                                
                            @endif
                            @foreach($lotevence as $l)
                                    <option value="{{ $l->id }}" style="font-size: .70rem;">
                                        {{ $l->mes ."/". $l->anno}} -> {{ $l->total }}
                                    </option>                                       
                            @endforeach                              

                        </select>	
                    </div>
                </div>
                @endif
                @if($lcantidad > 0)
                <div  class="form-group col-lg-3 col-md-3 col-sm-12 mt-3" style="font-size: .70rem;">
                    <button tabindex=8 wire:click="saveLine()"  type="button" acceskey="g" class="btn btn-sm btn-dark" style="font-size: .70rem;" tabindex=12>
                    Agregar(g) 
                    </button>
                </div>
                @endif
            </div>
            @endif
            @endif
            @if($detalle->count() > 0)
            <div class="row" id="inventarioDiv5">
            <div class="form-group col-lg-12 col-md-3 col-sm-12 mt-3">
                <div class="table-responsive-sm mt-2" style="font-size: .70rem;">
                    <table id="tabladetalle" name= "tabladetalle" style="font-size: .70rem;" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head">
                        <thead>
                            <tr>                                                   
                                <th class="text-center col-1" style="font-size: .70rem;">ACCIONES</th>
                                <th class="text-center col-2" style="font-size: .70rem;">BODEGA</th>
                                <th class="text-center col-4" style="font-size: .70rem;">ID - NOMBRE PRODUCTO</th>
                                <th class="text-center col-1" style="font-size: .70rem;">CNT</th>
                                <th class="text-center col-2" style="font-size: .70rem;">VECTO</th>
                            </tr>
                        </thead>
                        <tbody id="tbdetalle" name="tbdetalle" style="font-size: .70rem;">
                            @foreach($detalle as $d)
                                <tr>
                                    <td class="text-center" style="font-size: .70rem;">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);" style="font-size: .70rem;"
                                                onclick="Confirm('{{$d['reg']}}','{{$d['producto']}}')"
                                                data-toggle="tooltip" data-placement="top" title="Quitar Movimiento">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-left" style="font-size: .65rem;">{{$d['cbodega']}}</td>
                                    <td class="text-left" style="font-size: .65rem;"><b>{{$d['productoid']}}</b>-{{$d['producto']}}</td>
                                    <td class="text-center" style="font-size: .65rem;">{{$d['cantidad']}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['pcostou'],4)}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['subtotal'],4)}}</td>                            
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['iva'],4)}}</td>                            
                                    <td class="text-right" style="font-size: .70rem;">{{number_format($d['total'],2)}}</td>
                                    <td class="text-right" style="font-size: .70rem;">{{$d['cvence']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center " style="font-size: .70rem;"></th>
                                <th class="text-center " style="font-size: .70rem;"></th>                        
                                <th class="text-center " style="font-size: .70rem;"></th>
                                <th class="text-center " style="font-size: .70rem;"></th>                        
                                <th class="text-center " style="font-size: .70rem;">TOTAL:</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('subtotal'),4)}}</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('iva'),4)}}</th>
                                <th class="text-right " style="font-size: .70rem;">{{number_format($detalle->sum('total'),2)}}</th>                        
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if($detalle->sum('total')>0)
                    @if ($etapa==0)
                        <div id="botonGrabar" class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabari" wire:loading.attr="disabled" wire:target="grabaMovInventario" wire:click.prevent="grabaMovInventario()"
                                class="btn btn-sm btn-block btn-primary mt-4">Grabar Mv.Inventario</button>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btfiscal" wire:click="iniciaFiscal()"
                                class="btn btn-sm btn-block btn-success mt-4">>>Info Fiscal</button>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btdescarar" wire:click="descartaInventario()"
                                class="btn btn-sm btn-block btn-warning mt-4">Descartar Operación</button>
                            </div>
                        </div>
                    @else
                        <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabari" disabled class="btn btn-sm btn-block btn-primary mt-4">Grabar Mv.Inventario</button>
                            </div>

                        <div id="botonGrabar" class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btgrabar"  disabled class="btn btn-sm btn-block btn-success mt-4">Info Fiscal</button>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <button id="btdescarar" disabled class="btn btn-sm btn-block btn-warning mt-4">Descartar Operación</button>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            </div>            
            @endif

		</div>
	</div>
    @if($generandopda==1)
    <div class="col-xl-5 col-lg-5 col-md-12 col-12 layout-spacing" style="font-size: .70rem;">
        <div class="widget-content-area br-4" id="fiscal">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center" style="font-size: .70rem;">
                        <h5 style="font-size: .70rem;"><b>Información Fiscal y Contable del Movimiento</b></h5>
                    </div> 
                </div>
            </div>
       		<!--div datos head documento -->
            <div class="row">                
                <div class="col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Tipo Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <select wire:model.defer="tpdcfiscal"  wire:change="cambiaDoc($event.target.value)" class="form-control text-center" id="tipodoc" style="font-size: .75rem;" tabindex=1>  
                        <option value="" style="font-size: .70rem;">Elegir</option>
                        @foreach($docuprov as $dp)
                            <option value="{{$dp->id}}" style="font-size: .70rem;">
                                {{$dp->nombre}}
                            </option>                                       
                        @endforeach                              
                    </select>    
                    </div>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Fecha Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input type="date"  wire:model.defer="fechadoc" id="fechadoc" class="form-control" style="font-size: .70rem;" tabindex=2>
                    </div>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Pago Programado</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input tabindex=3 type="date" wire:model.lazy="fechaprograma" id="fechaprograma" class="form-control" style="font-size: .70rem;">
                    </div>
                </div>
            </div>
            <div class="row">                        
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">No.Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input tabindex=4 id ="numerodoc"  type="text" wire:model="numerofiscal" style="font-size: .70rem;" 
                        class="form-control" onkeypress="return CheckNumeric()">
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Unico No.</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input tabindex=5 id ="unico"  type="text" wire:model="unicofiscal" style="font-size: .70rem;"
                        class="form-control" onkeypress="return CheckNumeric()">
                    </div>
                </div>
                <div  class="col-lg-6 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Serie Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <div class="input-group-prepend input-group-sm" style="font-size: .70rem;">
                            <div class="input-group-text" style="font-size: .70rem;">
                                <a href="javascript:void(0);" onclick="nuevaSerie()"  data-toggle="tooltip" data-placement="top" title="Nueva Serie" style="font-size: .70rem;">
                                    <i class="la la-edit la-lg" data-toggle="tooltip" data-placement="top" title="Nueva Serie"></i>
                                </a>
                            </div>
                        </div>
                            <select tabindex=6 wire:model.lazy="serie"  wire:change="confirmaSerie($event.target.value)" class="form-control text-center" id="serie" style="font-size: .70rem;">   
                            <option value="" style="font-size: .70rem;">Elegir</option>
                            @foreach($seriesprov as $s)
                            <option value="{{ $s->id}}" style="font-size: .70rem;">
                                {{ $s->serie}}
                            </option>                                       
                            @endforeach                              
                        </select>    
                    </div>
                </div>
    		</div>
            <div class="row"> 
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Gravado</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <input tabindex=7 type="number" lang="en" wire:model.lazy="gravado" id="gravado" class="form-control"
                        placeholder="gravado" onkeypress="return CheckNumeric()" 
                        wire:keydown.enter="calculaG($event.target.value)" onblur=calcula() style="font-size: .70rem;">
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">IVA</h7>
                    <div class="input-group input-group-sm " style="font-size: .70rem;">
                        <input tabindex=8 type="number" lang="en" wire:model.lazy="iva" id="iva" class="form-control"
                        placeholder="iva" onkeypress="return CheckNumeric()" style="font-size: .70rem;"
                        wire:keydown.enter="calculaI($event.target.value)" onblur=calcula()>
                    </div>
                </div>

                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Percepción</h7>
                    <div class="input-group input-group-sm " style="font-size: .70rem;">
                        <input tabindex=9 type="number" lang="en" wire:model.lazy="percibido" id="percibe" class="form-control"
                        placeholder="percibido" onkeypress="return CheckNumeric()"
                        wire:keydown.enter="calculaP($event.target.value)" style="font-size: .70rem;" onblur=calcula()>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Renta</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <input tabindex=10 type="number" lang="en" wire:model.lazy="renta" id="renta" class="form-control"
                        placeholder="renta" onkeypress="return CheckNumeric()" style="font-size: .70rem;"
                        wire:keydown.enter="calculaR($event.target.value)" onblur=calcula()>
                    </div>
                </div>
            </div>
            <div class="row">            
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Descuento</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <input tabindex=11 type="number" lang="en" wire:model.lazy="vdescuento" id="descuento" class="form-control"
                        placeholder="Descuento" onkeypress="return CheckNumeric()" style="font-size: .70rem;"
                        wire:keydown.enter="calculaDs($event.target.value)" onblur=calcula()>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Flete</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .70rem;">
                        <input tabindex=12 type="number" lang="en" wire:model.lazy="flete" id="flete" class="form-control"
                        placeholder="Flete" onkeypress="return CheckNumeric()" style="font-size: .70rem;"
                        wire:keydown.enter="calculaFt($event.target.value)" onblur=calcula()>
                    </div>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .70rem;">Total</h7>
                    <div class="input-group input-group-sm" style="font-size: .70rem;">
                        <input tabindex=13 type="number" lang="en"  id="total" disabled class="form-control"
                            value='{{number_format($total,2)}}' placeholder="total documento" style="font-size: .70rem;">
                    </div>
                </div>
            </div>
            <div class="row">
                @if($total>0)
                    @if($etapa==1)
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <button id="btpda" wire:click="preparaPda()"
                            class="btn btn-sm btn-block btn-secondary mt-4">Generar Pda</button>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <button id="btdescartar" wire:click="descartaFiscal()"
                            class="btn btn-sm btn-block btn-danger mt-4">Descartar Operación</button>
                        </div>
                    @else
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <button id="btpda" disabled class="btn btn-sm btn-block btn-secondary mt-4">Generar Pda</button>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <button id="btdescartar" disabled class="btn btn-sm btn-block btn-danger mt-4">Descartar Operación</button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif

    @if ($paso==1)
        <div class="col-xl-12 col-12 col-md-12 col-12 layout-spacing" style="font-size: .70rem;">
            <div class="widget-content-area br-4" id="contable">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 text-center" style="font-size: .70rem;">
                            <h5 style="font-size: .70rem;"><b>Registro Contable</b></h5>
                        </div> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10 col-md-12 col-sm-12">
                        <div class="table-responsive-sm">
                            <table style="font-size: .70rem;" id="tabladetalle" name= "tabladetalle" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                                <thead>
                                    <tr>                                                   
                                        <th class="text-center col-1" style="font-size: .70rem;">ACCIONES</th>
                                        <th class="text-center col-4" style="font-size: .70rem;">CUENTA</th>
                                        <th class="text-center col-5" style="font-size: .70rem;">CONCEPTO</th>
                                        <th class="text-center col-1" style="font-size: .70rem;">CARGO</th>
                                        <th class="text-center col-1" style="font-size: .70rem;">ABONO</th>
                                    </tr>
                                </thead>
                                <tbody id="tbdetalle" name="tbdetalle" style="font-size: .70rem;">
                                @foreach($diario as $d)
                                <tr>
                                    <td class="text-center" style="font-size: .70rem;">
                                        <ul class="table-controls" style="font-size: .70rem;">
                                            <li style="font-size: .70rem;">
                                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Editar Registro de Partida"
                                                onclick="editaRegistro('{{$d->id}}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                            </li>

                                            <li style="font-size: .70rem;">
                                                <a href="javascript:void(0);"          		
                                                onclick="Confirm('{{$d->id}}')"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar Registro de Partida">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-left" style="font-size: .70rem;">{{$d->cuenta}}-<b>{{$d->nombrecuenta}}</b></td>
                                    <td class="text-left" style="font-size: .70rem;">{{$d->concepto}}</td>
                                    <td class="text-right" style="font-size: .70rem;">
                                        @if($d->debe!=0)
                                        {{number_format($d->debe,2,'.',',')}}
                                        @endif
                                    </td>
                                    <td class="text-right" style="font-size: .70rem;">
                                        @if($d->haber!=0)
                                        {{number_format($d->haber,2,'.',',')}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right" style="font-size: .70rem;"><b>Totales:</b></td>
                                        <td class="text-right" style="font-size: .70rem;"><b>{{number_format($totalescargos,2,'.',',')}}</b></td>
                                        <td class="text-right" style="font-size: .70rem;"><b>{{number_format($totalesabonos,2,'.',',')}}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-12">
                        <div class="btn-group-vertical" style="font-size: .70rem;">
                            <button type="button" onclick="agregaRegistro()" class="btn btn-sm btn-success mt-1" style="font-size: .70rem;">
                            Agrega Item Pda
                            </button>
                            <button type="button"  onclick="descartaP()" class="btn btn-sm btn-danger mt-1" style="font-size: .70rem;">
                            Descartar Operación
                            </button>
                            <button type="button"  onclick="grabaOperacion()" class="btn btn-sm btn-secondary mt-1" style="font-size: .70rem;">
                            Grabar Operación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>            
    @endif  

    @include('livewire.compras.editaregistromodal')            
    @include('livewire.compras.modalproveedor')            
    @include('livewire.compras.modalserie')    
</div>

@livewireScripts
<script type="text/javascript">
    var bPreguntar = true;
    window.onbeforeunload = preguntarAntesDeSalir;
    function preguntarAntesDeSalir()
    {
      if (bPreguntar)
       return "¿Seguro que quieres salir?";
    }
    datoslote=[];
    conlote=0;
    datosproducto=[];
    regs=0;
    registro=[];
    //variables totales documentos


   function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function nuevaSerie()
    {
        $('#lserie').val('')
        $('#modalserie').modal('show')
        $('#lserie').focus()
    }

    function nuevoProveedor()
    {
        validaFecha();
        //$('#mrazon').val('')
        //$('#mnit').val('')
        //$('#mnrc').val('')
        //$('#mduit').val('')
        //$('#mgiro').val('')

        $('#modalproveedor').modal('show')
        $('#mrazon').focus()
    }


    function saveRegPda()
    {
        $('#editaregistromodal').modal('hide')
        var cta = $('#selectcuenta').val()
        alert(cta)
        window.livewire.emit('grabarRegistro',cta);
    }

    function grabarenIva()
    {
        $('#modalgrabaiva').modal('show')
    }

    function asgBodega()
    {
        var vv=$('#bodega').val()
        window.livewire.emit('asgBodega', vv);
    }


    function saveProveedor()
    {
        if ($('#mrazon').val()=='')
        {
            toastr.error('Debe indicar un nombre de Proveedor...');
            return
        }
        if ($('#mnit').val()=='')
        {
            toastr.error('Debe indicar un Nùmero de Nit...')
            return
        }
         window.livewire.emit('grabaProveedor');
         //,$('mrazon').val(),$('mdui').val(),$('mnit').val(),$('mnrc').val(),$('mgiro').val()
         $('#modalproveedor').modal('hide')
    }    


    function saveSerie()
    {
        if ($('lserie').val('') != '')
        {
         window.livewire.emit('saveSerie',$('lserie').val());
         $('#modalserie').modal('hide')

        }
        else
        {
            toastr.error('No se puede grabar una serie en Blanco...');
        }
    }

   function Confirm(id,prod)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL REGISTRO: '+prod+'?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
        },
        function()
        {

            window.livewire.emit('deleteRow', id)    

            toastr.success('', 'Registro eliminado con éxito')

            swal.close()
        })
    }

   function valCantidad()
    {
        vv=Number($('#cantidad').val())
        v1=Number($('#costounitario').val())

        window.livewire.emit('validaCantidad', vv,v1)    
    }

   function muestraNumero(id)
    {
       swal(
        {
        title: 'Confirmación',
        text: 'La Compra fue Grabado con el movimiento No.: '+id,
        type: 'success',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar',
        closeOnConfirm: true
        })
   }

    function roundTo2(num)
    {
        var valor = num.value;
       num.value =  parseFloat(Number(valor)).toFixed(2);
    }

    function saveProveedor()
    {
        if($('#mrazon').val() == '')
        {
           toastr.error('Debe indicar un nombre de Proveedor....')
           return;
        }
        if($('#mnit').val() == '' && $('#mdui').val() == '')
        {
           toastr.error('Debe indicar un NIT ó DUI del Proveedor....')
           return;
        }
        window.livewire.emit('grabaProveedor')
        $('#modalproveedor').modal('hide')
    }

    function calcula()
    {
        window.livewire.emit('calculaT');
    }

    function agregaRegistro()
    {
        window.livewire.emit('agregaRegistro');
    }


    function grabaOperacion()
    {
        window.livewire.emit('grabaProceso');
    }

    function validaCargo()
    {
        window.livewire.emit('validaCargo',Number($('#rgcargo').val()));
    }
    function validaAbono()
    {
        window.livewire.emit('validaAbono',Number($('#rgabono').val()));
    }
    function editaRegistro(id)
    {
        window.livewire.emit('editaRegistro',id)
    }

    function descartaP()
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿Seguro de Descartar EL PROCESO CONTABLE ACTUAL ????',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
        },
        function()
        {
        window.livewire.emit('descartarOperacion');   
        swal.close()
        })
    }

    function mostrarDatos()
    {
    datosproducto = document.getElementById('selectcuenta').value.split('_');

    $('#selectcuenta').val(datosproducto[0]);

    }
    function saveLine()
    {
        $('#editaregistromodal').modal('hide')
        window.livewire.emit('grabarRegistro',$('#selectcuenta').val());
    }


    document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('print-mov', idMov => {
        var wactual = window;
        var ruta = "{{url('detallemov')}}" + '/' + idMov;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })

        window.livewire.on('confirmaOrden',idord=>
        {
            muestraNumero(idord);
        })

        window.livewire.on('loteVenceMask', nuevoP =>{
            $('.lotevence').mask('00/0000');
        })

        window.livewire.on('nuevoProveedor', nuevoP =>{
            $('#modalproveedor').modal('show')
            $('.money').mask('000,000,000.0000', {reverse: true});
  	    	$('.movil').mask('0000-0000');
      		$('.dui').mask('00000000-0');
  		    $('.nit').mask('0000-000000-000-0');		 
            $('#mrazon').focus();
        })

        window.livewire.on('abreModalRg', idMov =>
        {
            $('#editaregistromodal').modal('show')
            $('#selectcuenta').select2({
             dropdownParent: $('#editaregistromodal')
            });
            //$('#selectcuenta').val(idMov)
            $('#selectcuenta').focus()

        })

        window.livewire.on('noInventarioDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("inventario").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("inventario").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })

        window.livewire.on('noFiscalDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("fiscal").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("fiscal").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })

        window.livewire.on('noContableDiv', idMov =>
        {
            if (idMov==0)
            {
                var childNodes = document.getElementById("contable").getElementsByTagName('*');
                for (var node of childNodes)
                {
                    node.disabled = true;
                }   
            }
            else
            {
                var childNodes = document.getElementById("contable").getElementsByTagName('*');
                for (var node of childNodes)
                {
                   node.disabled = false;
                   // node.removeAttr('disabled');                    
                }   
            }
        })


   })
 
</script>