<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-2">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center" style="font-size: .70rem;">
                        <h5><b>Entrada de Compra de Producto</b></h5>
                    </div> 
                </div>
            </div>
       		<!--div datos head documento -->
            <div class="row" x-data="{ isOpen: true }" @click.away="isOpen = false" >
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
               		<h7 class="text-info" style="font-size: .75rem;">Fecha Ingreso</h7>
               		<div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .75rem;">
                   		<div class="input-group-prepend" style="font-size: .75rem;">
                       		<div class="input-group-text" style="font-size: .75rem;">
								<i class="la la-calendar la-lg"></i>
							</div>
                   		</div>
                   		<input type="date" wire:model.defer="lafecha" id="lafecha" class="form-control" style="font-size: .75rem;"  tabindex=1>
                   	</div>
               	</div>
                <!--div buscador Cliente-->        
                <div class="form-group col-lg-6 col-md-4 " style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Buscador de Proveedor</h7>
                    <div class="input-group input-group-sm mr-sm-2" style="font-size: .75rem;">
                        <div class="input-group-prepend" style="font-size: .75rem;">
                            <div class="input-group-text" style="font-size: .75rem;">
                                <a href="javascript:void(0);" wire:click=iniciarProveedor() data-toggle="tooltip" data-placement="top" title="Nuevo Proveedor" style="font-size: .75rem;">    
                                <i class="la la-file la-lg" ></i></a>
                            </div>
                        </div>
                        <input type="hidden" wire:model.lazy="sujeto_id" id="idprovee">
                        <input type="text" tabindex=2 class="form-control" placeholder="Buscar Proveedor..." style="font-size: .75rem;"
                            onkeyup="javascript:this.value=this.value.toUpperCase();"
                        wire:model="lsujeto" id="proveedor" @focus="isOpen = true" @keydown.escape.window="isOpen = false"
                        @keydown.shift.tab="isOpen = false" @keydown.tab="isOpen = false">
                        <div class="input-group-prepend" style="font-size: .75rem;">
                            <div class="input-group-text" style="font-size: .75rem;">
                                <a href="javascript:void(0);" onclick="limpiaProvee()"  data-toggle="tooltip" data-placement="top" title="Limpiar Proveedor" style="font-size: .75rem;">
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
			</div>
            <div class="row">                
                <div class="col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Tipo Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <select wire:model.defer="tpdcfiscal"  wire:change="cambiaDoc($event.target.value)" class="form-control text-center" id="tipodoc" style="font-size: .75rem;" tabindex=4>  
                        <option value="" style="font-size: .75rem;">Elegir</option>
                        @foreach($docuprov as $dp)
                            <option value="{{$dp->id}}" style="font-size: .75rem;">
                                {{$dp->nombre}}
                            </option>                                       
                        @endforeach                              
                    </select>    
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Fecha Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <div class="input-group-prepend" style="font-size: .75rem;">
                            <div class= "input-group-text" style="font-size: .75rem;">
                                <i class="la la-calendar la-lg"></i>
                            </div>
                        </div>
                            <input type="date"  wire:model.defer="fechadoc" id="fechadoc" class="form-control" style="font-size: .75rem;" tabindex=5>
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Número Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input tabindex=6 id ="numerodoc"  type="text" wire:model="numerofiscal" style="font-size: .75rem;" 
                        class="form-control" onkeypress="return CheckNumeric()">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Form Unico No.</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input tabindex=7 id ="unico"  type="text" wire:model="unicofiscal" style="font-size: .75rem;"
                        class="form-control" onkeypress="return CheckNumeric()">
                    </div>
                </div>
                <div  class="col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Serie Documento</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <div class="input-group-prepend input-group-sm" style="font-size: .75rem;">
                            <div class="input-group-text" style="font-size: .75rem;">
                                <a href="javascript:void(0);" onclick="nuevaSerie()"  data-toggle="tooltip" data-placement="top" title="Nueva Serie" style="font-size: .75rem;">
                                    <i class="la la-edit la-lg" data-toggle="tooltip" data-placement="top" title="Nueva Serie"></i>
                                </a>
                            </div>
                        </div>
                            <select tabindex=8 wire:model.lazy="serie"  wire:change="confirmaSerie($event.target.value)" class="form-control text-center" id="serie" style="font-size: .75rem;">   
                            <option value="" style="font-size: .75rem;">Elegir</option>
                            @foreach($seriesprov as $s)
                            <option value="{{ $s->id}}" style="font-size: .75rem;">
                                {{ $s->serie}}
                            </option>                                       
                            @endforeach                              
                        </select>    
                    </div>
                </div>
    		</div>
            <div class="row"> 
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Gravado</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .75rem;">
                        <input tabindex=9 type="number" lang="en" wire:model.lazy="gravado" id="gravado" class="form-control"
                        placeholder="gravado" onkeypress="return CheckNumeric()" 
                        wire:keydown.enter="calculaG($event.target.value)" onchange="calcula()" onblur="calcula()" style="font-size: .75rem;">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Percepción</h7>
                    <div class="input-group input-group-sm " style="font-size: .75rem;">
                        <input tabindex=10 type="number" lang="en" wire:model.lazy="percibido" id="percibe" class="form-control"
                        placeholder="percibido" onkeypress="return CheckNumeric()"
                        wire:keydown.enter="calculaP($event.target.value)" onchange="calcula()" onblur="calcula()" style="font-size: .75rem;">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Renta</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .75rem;">
                        <input tabindex=11 type="number" lang="en" wire:model.lazy="renta" id="renta" class="form-control"
                        placeholder="renta" onkeypress="return CheckNumeric()" style="font-size: .75rem;"
                        wire:keydown.enter="calculaR($event.target.value)" onchange="calcula()" onblur="calcula()">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">IVA</h7>
                    <div class="input-group input-group-sm " style="font-size: .75rem;">
                        <input tabindex=12 type="number" lang="en" wire:model.lazy="iva" id="iva" class="form-control"
                        placeholder="iva" onkeypress="return CheckNumeric()" style="font-size: .75rem;"
                        wire:keydown.enter="calculaI($event.target.value)" onchange="calcula()" onblur="calcula()">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Total</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input tabindex=13 type="number" lang="en" wire:model.lazy="total" id="total" disabled class="form-control"
                            maxlength="50" placeholder="total documento" style="font-size: .75rem;">
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Pago Programado</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input tabindex=14 type="date" wire:model.lazy="fechaprograma" id="fechaprograma" class="form-control" style="font-size: .75rem;">
                    </div>
                </div>
            </div>
        </div>
    </div>       
    @if($sujetoid!='')    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center" style="font-size: .70rem;">
                        <h5><b>Detalle de Ingreso a Bodega</b></h5>
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-2 col-md-4 col-sm-12" style="font-size: .70rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Bodega</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
    					<select tabindex=3 wire:model.defer="bodega"  id="bodega" wire:change="asgBodega($event.target.value)" class="form-control text-center" style="font-size: .75rem;">
 	    				<option value="" style="font-size: .75rem;">Elegir</option>
                        @foreach($bodegas as $bd)
                                <option value="{{ $bd->id }}" style="font-size: .75rem;">
                                    {{ $bd->nombre}}
                                </option>                                       
                        @endforeach                              
                        </select>	
                    </div>
				</div>
                @if($bodega>0)
                <!--div buscador producto-->        
                <div class="form-group col-lg-5 col-md-12 "  id="buscaPro1" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Buscador de Producto</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <div class="input-group-prepend" style="font-size: .75rem;">
                            <div class="input-group-text" style="font-size: .75rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"   class="form-control" id="buscadorproducto"  placeholder="Buscar Producto..." style="font-size: .75rem;"
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
                <!--fin buscador producto-->
                <div style="display: none;"  id ="bt00" class="form-group col-lg-2 col-md-6 " style="font-size: .75rem;">
                    <h7 class="text-info">Stock</h7>
                    <div class="input-group input-group-sm" >
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  disabled class="form-control"  value='{{$lstock}}' style="font-size: .75rem;">
                    </div>
                </div>
                <div style="display: none;"  id ="bt0" class="form-group col-lg-2 col-md-6 " style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Precio Venta (s/iva)</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;" >
                        <div class="input-group-prepend" style="font-size: .75rem;">
                            <div class="input-group-text" style="font-size: .75rem;">
                                <i class="la la-search la-lg"></i>
                            </div>
                        </div>
                        <input type="text"  disabled class="form-control"  value='{{number_format($lpreventa,4)}}' style="font-size: .75rem;">
                    </div>
                </div>
            </div>
            <div class="row">
                <div style="display: none;"  id ="bt3" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .75rem;">   
                    <h7 class="text-info" style="font-size: .75rem;">Cantidad</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input type="text"   id="cantidad" wire:model.defer="lcantidad" style="font-size: .75rem;" 
                        wire:change="validaCantidad($event.target.value)" wire:keydown.enter="validaCantidad($event.target.value)"
                        onkeypress="return CheckNumeric()" class="form-control" placeholder="cantidad">
                    </div>
                </div>

                <div style="display: none;"  id ="bt4" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Costo Uni</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input type="text"  wire:model.defer="lprecosto" wire:change="validaCantidad($event.target.value)" wire:keydown.enter="validaCantidad($event.target.value)"
                        style="font-size: .75rem;" onkeypress="return CheckNumeric()" id="costounitario"  class="costo form-control" placeholder="costo">
                    </div>
                </div>
                <div style="display: none;"  id ="bt5" class="form-group col-lg-2 col-md-3 col-sm-12" style="font-size: .75rem;">   
                    <h7 class="text-info" style="font-size: .75rem;">Total</h7>
                    <div class="input-group input-group-sm" style="font-size: .75rem;">
                        <input type="text" id="montoproducto" value='{{number_format($lmonto,2)}}'  class="costo form-control" style="font-size: .75rem;"
                        disabled placeholder="total">
                    </div>
                </div>
                <div style="display: none;"  id ="bt1" class="form-group col-lg-2 col-md-6 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;">Lote Vence</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .75rem;">
                        <select   wire:model.defer="loteid" id="lote"  wire:change="asgLote($event.target.value)" class="form-control text-center">
                            @if($lotesprodsum>1)
                                <option value="" style="font-size: .75rem;">Seleccionar Lote</option>
                                <option value="" style="font-size: .75rem;">Sin Lote</option>                                
                            @endif
                            @if($lotesprodsum==1)
                                <option value="" style="font-size: .75rem;">Sin Lote</option>                                
                            @endif
                            @foreach($lotevence as $l)
                                    <option value="{{ $l->id }}" style="font-size: .75rem;">
                                        {{ $l->mes ."/". $l->anno}}
                                    </option>                                       
                            @endforeach                              

                        </select>	
                    </div>
                </div>
                <div style="display: none;"  id ="bt2" class="form-group col-lg-2 col-md-6 col-sm-12" style="font-size: .75rem;">
                    <h7 class="text-info" style="font-size: .75rem;"><b>Nuevo Lote</b></h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2" style="font-size: .75rem;">
                        <input type="text"  wire:model.defer="newlote" wire:change="validaNuevoLote($event.target.value)"
                        wire:keydown.enter="validaNuevoLote($event.target.value)" id="lotenew" style="font-size: .75rem;"
                        class="lotevence form-control" placeholder="00/0000">
                    </div>
                </div>
                @if($todobien==4)
                <div  class="form-group col-lg-2 col-md-3 col-sm-12 mt-3">                       
                    <button  wire:click="saveLine()"  type="button" acceskey="g" class="btn btn-sm btn-dark">
                    Agregar(g)
                    </button>
                </div>
                @endif
            </div>

            <div class="table-responsive-sm mt-2" style="font-size: .75rem;">
            <table id="tabladetalle" name= "tabladetalle" style="font-size: .75rem;" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                <thead>
                    <tr>                                                   
                        <th class="text-center col-1" style="font-size: .75rem;">ACCIONES</th>
                        <th class="text-center col-1" style="font-size: .75rem;">BODEGA</th>
                        <th class="text-center col-4" style="font-size: .75rem;">ID - NOMBRE PRODUCTO</th>
                        <th class="text-center col-1" style="font-size: .75rem;">CANTIDAD</th>
                        <th class="text-center col-1" style="font-size: .75rem;">CST UNIT</th>
                        <th class="text-center col-1" style="font-size: .75rem;">SUB-TOTAL</th>                        
                        <th class="text-center col-1" style="font-size: .75rem;">IVA</th>                        
                        <th class="text-center col-1" style="font-size: .75rem;">MONTO</th>
                        <th class="text-center col-2" style="font-size: .75rem;">VECTO</th>
                    </tr>
                </thead>
                <tbody id="tbdetalle" name="tbdetalle" style="font-size: .75rem;">
                    @foreach($detalle as $d)
                        <tr>
                            <td class="text-center" style="font-size: .75rem;">
                                <ul class="table-controls">

                                    <li>
                                        <a href="javascript:void(0);" style="font-size: .75rem;"
                                        onclick="Confirm('{{$d['reg']}}','{{$d['producto']}}')"
                                        data-toggle="tooltip" data-placement="top" title="Quitar Movimiento">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </a>
                                    </li>
                                </ul>
                            </td>
                            <td class="text-left" style="font-size: .75rem;"><b>{{$d['cbodega']}}</b></td>
                            <td class="text-left" style="font-size: .75rem;"><b>{{$d['productoid']}}</b>-{{$d['producto']}}</td>
                            <td class="text-center" style="font-size: .75rem;">{{$d['cantidad']}}</td>
                            <td class="text-right" style="font-size: .75rem;">{{number_format($d['pcostou'],4)}}</td>
                            <td class="text-right" style="font-size: .75rem;">{{number_format($d['subtotal'],4)}}</td>                            
                            <td class="text-right" style="font-size: .75rem;">{{number_format($d['iva'],4)}}</td>                            
                            <td class="text-right" style="font-size: .75rem;">{{number_format($d['monto'],2)}}</td>
                            <td class="text-right" style="font-size: .75rem;">{{$d['cvence']}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center col-1" style="font-size: .75rem;"></th>
                        <th class="text-center col-1" style="font-size: .75rem;"></th>                        
                        <th class="text-center col-1" style="font-size: .75rem;"></th>
                        <th class="text-center col-4" style="font-size: .75rem;"></th>                        
                        <th class="text-center col-1" style="font-size: .75rem;">TOTAL:</th>
                        <th class="text-right col-1" style="font-size: .75rem;">{{number_format($detalle->sum('subtotal'),4)}}</th>
                        <th class="text-right col-1" style="font-size: .75rem;">{{number_format($detalle->sum('iva'),4)}}</th>
                        <th class="text-right col-1" style="font-size: .75rem;">{{number_format($detalle->sum('total'),4)}}</th>                        
                    </tr>
                </tfoot>
            </table>
            </div>
            @if($montot>0)
            <div id="botonGrabar" class="row justify-content-between">
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <button id="btgrabar" wire:click="grabarBd()"
                    class="btn btn-primary mt-4">Grabar la Operación</button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <button id="btdescarar" wire:click="descartarOperacion()"
                    class="btn btn-warning mt-4">Descartar Operación</button>
                </div>
            </div>
            @endif
		</div>
	</div>
    @endif
    @include('livewire.compras.modal')    
    @include('livewire.compras.modalproveedor')            

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

    function prueba()
    {
        vv=$('#cantidad').val()

        alert(vv)
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

        window.livewire.on('enable-cajas', accion=>
        {
            $('.lotevence').mask('00/0000');

            
            div = document.getElementById('bt00')
            div.style.display ='block'

            div = document.getElementById('bt0')
            div.style.display ='block'
            //
            if (accion.lote>0)
            {
                div = document.getElementById('bt1')
                div.style.display ='block'
            }
            else
            {
                div = document.getElementById('bt1')
                div.style.display ='none'

            }

            div = document.getElementById('bt2')
            div.style.display ='block'
            div = document.getElementById('bt3')
            div.style.display ='block'
            div = document.getElementById('bt4')
            div.style.display ='block'
            div = document.getElementById('bt5')
            div.style.display ='block'
            //
            if (accion.muestrac==1)
            {
                $('#cantidad').focus();
            }
        })

        window.livewire.on('nuevoProveedor', nuevoP =>{
            $('#modalproveedor').modal('show')
            $('.money').mask('000,000,000.0000', {reverse: true});
  	    	$('.movil').mask('0000-0000');
      		$('.dui').mask('00000000-0');
  		    $('.nit').mask('0000-000000-000-0');		 
            $('#mrazon').focus();
        })

   })
 
</script>