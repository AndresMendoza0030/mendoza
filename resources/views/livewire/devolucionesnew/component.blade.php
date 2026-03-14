<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing" x-data="{ isOpen: true }" @click.away="isOpen = false">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Devolución Producto/Servicio Cta Paciente</b></h5>
                    </div> 
                </div>
            </div>

            <div class="row mt-3">
                <div class="form-grou col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Seleccionar Paciente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.defer="rpaciente" id="slpaciente" class="form-control text-center">
 						<option value="">Elegir</option>
 	    				@foreach($pacientes as $pac)
 							<option value="{{ $pac->idpaciente }}" wire:click="mostrarPaciente({{$pac}})">
 						    {{ $pac->nombrepaciente}}
 							</option>                                       
						@endforeach                              
 					</select>	
                    </div>
				</div>

                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Md.Referente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="elmedico" id="elmedico" class="form-control">
                    </div>
				</div>
           		<div class="form-group col-lg-1 col-md-4 col-sm-12">
               		<h7 class="text-info">Cuarto</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="elcuarto" id="elcuarto" class="form-control">
                    </div>
               	</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">FECHA Y HORA ADMISION</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model="fechayhora" id="fechayhora" class="form-control">
                    </div>
               	</div>
            </div>      
            <!--div datos head documento -->
            <div class="row">                     
                @if(count($vales) > 0)
                <div class="form-grou col-lg-4 col-md-12 col-sm-12">
                    <h7 class="text-info">Vales de Cargo por Paciente</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
					<select wire:model.defer="rvale" id="slvales" class="form-control text-center">
						<option value="">Elegir</option>
 	    				@foreach($vales as $vl)
 							<option value="{{ $vl->id }}" wire:click="mostrarVale({{$vl}})">
 						    {{ $vl->infovale}}
 							</option>                                       
						@endforeach 
 					</select>	
                    </div>
				</div>
                @endif

                <div class="form-group col-lg-2 col-md-4 col-sm-12">
                    <h7 class="text-info">Documento No.</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.lazy="documento"  class="form-control">
                    </div>
				</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">Fecha Doc.</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.lazy="fechadoc"  class="form-control">
                    </div>
               	</div>
           		<div class="form-group col-lg-2 col-md-4 col-sm-12">
               		<h7 class="text-info">Bodega Operación</h7>
                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                        <input type="text" disabled wire:model.lazy="labodega"  class="form-control">
                    </div>
               	</div>
			</div>

            <div class="row">                     
                <div class="col-lg-6">
                    <div class="card text-center">
                        <h7 class="card-header"><b>Detalle Cargo Paciente</b></h7>
                    </div>
                    <div class="table-responsive-sm ">
                        <table id="tbcargo" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                            <thead>
                                <tr>                                                   
                                    <th class="text-center col-2">ACCIONES</th>
                                    <th class="text-center col-5">DESCRIPCIÓN</th>
                                    <th class="text-center col-1">CANT</th>
                                    <th class="text-center col-1">VCTO</th>
                                    <th class="text-center col-1">DEVU</th>
                                    <th class="text-center col-1">P.UNIT.</th>                        
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($info) > 0)
                            @foreach($info as $r)
                                <tr>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);"          		
		                                        onclick="editLine('{{$r}}')"
		                                        data-toggle="tooltip" data-placement="top" title="Devolución Parcial">
                                                <svg id="Flat" height="24" viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m256 488a229.844 229.844 0 0 1 -141.467-48.108 233.557 233.557 0 0 1 -82.025-121.445 24 24 0 1 1 46.235-12.894 185.25 185.25 0 0 0 65.083 96.312 182.236 182.236 0 0 0 112.174 38.135c101.458 0 184-82.542 184-184s-82.542-184-184-184a182.241 182.241 0 0 0 -112.175 38.135 185.254 185.254 0 0 0 -65.082 96.312 24 24 0 1 1 -46.235-12.894 233.559 233.559 0 0 1 82.025-121.444 232.008 232.008 0 0 1 373.467 183.891 232 232 0 0 1 -232 232z" fill="#8690fa"/><path d="m56 224a24 24 0 0 1 -23.964-22.669l-8-144a24 24 0 1 1 47.924-2.662l6.32 113.761 106.941-31.454a24 24 0 1 1 13.543 46.048l-135.995 40a23.947 23.947 0 0 1 -6.769.976z" fill="#5153ff"/></svg>
                                                </a>
	                                        </li>
	                                        <li>
		                                        <a href="javascript:void(0);"          		
		                                        onclick="devolverTodo('{{$r}}')"
		                                        data-toggle="tooltip" data-placement="top" title="Devolución Total">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                            <li>
                                        </ul>
                                    </td>
                                    <td>{{$r->producto}}</p></td>
                                    <td>{{$r->cantidad}}</td>

                                    @if($r->lotevence != 0)
                                        <td>{{$r->mes}}/{{$r->anno}}</td>
                                    @endif
                                    @if($r->lotevence == 0)
                                    <td> </td>
                                    @endif
                                    <td>{{$r->devuelto}}</td>
                                    <td>{{$r->preventa}}</td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                        @if(count($info) > 0)
                                <div class="col-md-8 col-lg-8 col-sm-12">
                                    <button id="btaddall" onclick="todoDocumento('{{$info}}')"
                                    class="btn btn-sm btn-danger">Devuelve todo el Producto</button>
                                </div>
                        @endif

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card text-center">
                         <h7 class="card-header"><b>Producto a Devolver</b></h7>
                    </div>
                    <div class="table-responsive-sm ">
                        <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                            <thead>
                                <tr>                                                   
                                    <th class="text-center col-5">ACCION</th>
                                    <th class="text-center col-5">DESCRIPCIÓN</th>
                                    <th class="text-center col-1">CANT</th>
                                    <th class="text-center col-1">VCTO</th>
                                    <th class="text-center col-1">M.VTA</th>                        
                                </tr>
                            </thead>
                            <tbody id="tbdetalle">
                            </tbody>
                        </table>
                        <div style="display: none;" id="quitatodo" class="col-md-8 col-lg-8 col-sm-12">
                            <button id="limpiar" onclick="quitaTodoDocumento()"
                            class="btn btn-sm btn-warning">Limpiar Devolución</button>
                        </div>
                    </div>
                </div>
		    </div>
        </div>
        <div style="display: none;"  id="botonGrabar" class="row justify-content-between">
    		<div class="col-md-4 col-lg-4 col-sm-12">
               	<button id="btgrabar" onclick="grabarBD()"
		 		class="btn btn-success mt-4">Grabar</button>
            </div>
	    </div>
        @include('livewire.devoluciones.modal')    
	</div>
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

    datosproducto=[];
    datospaciente=[];
    regs=0;
    registro=[];
    var detalle=[];
    accion=1;
    editno=0;
    //variables totales documentos


   function validarDevolver()
   {
        if (Number($('#cdevolver').val()) > Number($('#coriginal').val())) 
        {
            toastr.error('La cantidad a devolver no puede ser mayor que la salida...');
            return;
        }
        if (Number($('#cdevolver').val()) > (Number($('#coriginal').val())-Number($('#hdevuelto').val()))) 
        {
            toastr.error('La cantidad a devolver no puede ser mayor que la salida...');
            return;
        }

    }

    function editLine(rw)
   {
       var info = JSON.parse(rw);

        if(info.devuelto<info.cantidad)
            {
            if (validaCodigo(info.producto_id))
                {
                    $('#mdproducto').val(info.producto)
                    $('#manejopor').val(info.manejopor)
                    $('#coriginal').val(info.cantidad)
                    $('#cdevolver').val('')
                    //
                    $('#hdocumento').val(info.numerodoc)
                    $('#hbodega').val(info.bodega)
                    $('#hproducto').val(info.producto)
                    $('#hidproducto').val(info.producto_id)
                    $('#hdevuelto').val(info.devuelto)
                    $('#hunitario').val(info.preventa)
                    $('#hunico').val(info.id)
                    if (info.lotevence != 0)
                    {
                        $('#hlotevence').val(info.lotevence)
                        $('#hclotevence').val(info.mes+'/'+info.anno)
                    }
                    else
                    {
                        $('#hlotevence').val('')
                        $('#hclotevence').val('')
                    }
                    $('#hidpaciente').val(info.paciente_id)
                    //
                    $('#cdevolver').focus()
                    $('#modalLine').modal('show')
                }
            }
        else
        {
            toastr.error('Ya no existe producto para devolver...');            
        }
   }



   function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}


    function saveLine()
    {
       if($('#cdevolver').val() == '')
       {
           toastr.error('No hay cantidad digitada a devolver...')
           return;
       }
    //llenando arreglo
    registro[regs]= {noreg:regs,elproducto:$('#hproducto').val(),
    bodega:$('#hbodega').val(),idproducto:$('#hidproducto').val() ,
    adevolver:$('#cdevolver').val(), devuelto:$('#hdevuelto').val(),
    documento:$('#hdocumento').val(),unitario:$('#hunitario').val(),
    monto:(Number($('#cdevolver').val())*Number($('#hunitario').val())).toFixed(4),
    unico:$('#hunico').val(),idpaciente:$('#hidpaciente').val(),
    lotevence:Number($('#hlotevence').val()), clotevence:$('#hclotevence').val()};
    //
    creaFilaHtml()
    $('#modalLine').modal('hide');
    //
    }

    function creaFilaHtml()
    {
    //fila html
        filaHtml='<tr id="fila'+regs+'"><td class="text-center">'+
        '<ul class="table-controls">'+
	    '<li><a href="javascript:void(0);"onclick="Confirm('+regs+')"'+
        'data-toggle="tooltip" data-placement="top" title="Delete"><svg xmlns="http://www.w3.org/2000/svg"'+
        'width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"'+
        'stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger">'+
        '<polyline points="3 6 5 6 21 6"></polyline>'+
        '<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">'+
        '</path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17">'+
        '</line></svg></a></li>'+
        '</ul></td>'+     
        '<td>'+registro[regs].elproducto+'</td>'+
        '<td id="fila'+regs+'cl2" class="text-center">'+registro[regs].adevolver+'</td>'+
        '<td id="fila'+regs+'cl22" class="text-center">'+registro[regs].clotevence+'</td>'+
        '<td id="fila'+regs+'cl4" class="text-center">'+registro[regs].monto+'</td>'+
        '</tr>'; 
        $('#tbdetalle').append(filaHtml);
        //
        regs++
        if (regs > 0)
        {
            $('#slvales').attr("disabled", "disabled");
            $('#slpaciente').attr("disabled", "disabled");

            $('#botonGrabar').show();
        }
    }


   function validaCodigo(xcodigo)
   {
    for (var i=0; i<registro.length ; i++)
    {
	    if (registro[i].idproducto == xcodigo)
		{
            toastr.error('', 'El Código ya Existe en el Documento a Devolver....')
            return false;	
		}
	}
    return true;
   }

   function todoDocumento(row)
   {
       var docdato= JSON.parse(row);

        if (registro.length>0)
        {
            quitaTodoDocumento();
        }
       x=docdato.length
       regs=0;
       indice=0;
       registro=[];
       while (indice<x)
       {
           
           if (docdato[indice].devuelto < docdato[indice].cantidad)
           {

                if (docdato[indice].lotevence == 0)
                {
                    xlote=''
                    xclote=''
                }
                else
                {
                    xlote = docdato[indice].lotevence
                    xclote= docdato[indice].mes+'/'+docdato[indice].anno
                }
                
                registro[regs]= {noreg:indice,elproducto:docdato[indice].producto,
                bodega:docdato[indice].bodega, idproducto:docdato[indice].producto_id,
                adevolver:(docdato[indice].cantidad-docdato[indice].devuelto),
                devuelto:docdato[indice].devuelto,documento:docdato[indice].numerodoc,
                unitario:docdato[indice].preventa,monto:docdato[indice].gravado,unico:docdato[indice].id,
                lotevence:xlote,clotevence:xclote};
                //
                creaFilaHtml();
                //
                div = document.getElementById('quitatodo');
                div.style.display ='block';
           }
           indice++;
       }
   }

   function quitaTodoDocumento(row)
   {
       x=0;
       tp=registro.length
       regs=0;
       registro=[];
       while (x<tp)
       {
            $('#fila'+x).remove();
            //
            x++;
            div = document.getElementById('quitatodo');
            div.style.display ='none';
            div = document.getElementById('botonGrabar');
            div.style.display ='none';

       }
   }



   function devolverTodo(row)
   {
    var info = JSON.parse(row);
    //llenando arreglo
    if (validaCodigo(info.producto_id))
        {
            if (info.devuelto<info.cantidad)
            {
                if (info.lotevence == 0)
                {
                    xlote=''
                    xclote=''
                }
                else
                {
                    xlote = info.lotevence
                    xclote= info.mes+'/'+info.anno
                }

                registro[regs]= {noreg:regs,elproducto:info.producto,
                bodega:info.bodega, idproducto:info.producto_id,adevolver:(info.cantidad-info.devuelto),
                devuelto:info.devuelto,documento:info.numerodoc,unitario:info.preventa,
                monto:info.gravado,unico:info.id,lotevence:xlote,clotevence:xclote};
                //
                creaFilaHtml()
                //
            }
            else
            {
                toastr.error('Ya no existe producto para devolver...');            

            }
        }
   }

   function Confirm(id)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL REGISTRO?',
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
            //window.livewire.emit('deleteRow', id)    
		    if (registro.length==1)
    		{
			regs=0;
			registro=[];
            $('#botonGrabar').hide()
            $('#slvales').removeAttr("disabled");
            $('#slpaciente').removeAttr("disabled");
            
		    }
		    else
		    {
    			registro.splice(id,1);
		    }
    	    $('#fila'+id).remove();        
            toastr.success('', 'Registro eliminado con éxito')
            swal.close()
        })
   }

   function grabarBD()
   {
    if(regs == '0')
       {
           toastr.error('No se puede grabar sin detalle de productos....')
           return;
       }

    var datos = JSON.stringify(registro);
    window.livewire.emit('grabarDatos',datos)
    //
    div = document.getElementById('quitatodo');
    div.style.display ='none';
    div = document.getElementById('botonGrabar');
    div.style.display ='none';
    $('#btaddall').hide();
    $('#slpaciente').val('Elegir');    
    $('#slvales').hide();
    //
   }

   document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('msg-ok', dataMsg =>{ $('#modalCompraProd').modal('hide')})

        window.livewire.on('print-mov', idMov => {
        var wactual = window;
        var ruta = "{{url('detallemov')}}" + '/' + idMov;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })


   })
   
</script>