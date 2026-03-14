<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>REPORTES DE CONTROL CONSUMO</b></h5>
                    </div> 
                </div>
            </div>

                <div class="card border-info ml-2 mr-2 mb-1">
                    <div class="table-responsive-sm mt-2 mb-2">
                        <table id="tabladetalle" name= "tabladetalle" class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-1">
                        <thead>
                            <tr>                                                   
                                <th class="text-center col-3" style="font-size: .70rem;">NOMBRE DEL REPORTE</th>
                                <th class="text-center col-3" style="font-size: .70rem;">PARAMETROS</th>                                
                                <th class="text-center col-1" style="font-size: .70rem;">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left" style="font-size: .70rem;">Consumos x Destino, Sumarizadas x Período</td>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-md-12 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-secondary" style="font-size: .70rem;">Desde</h7>
                                            <div  class="input-group input-group-sm ">
                                                <input type="date"  wire:model.lazy="desde" class="form-control" style="font-size: .70rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-12 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-secondary" style="font-size: .70rem;">Hasta</h7>
                                            <div  class="input-group input-group-sm ">
                                                <input type="date"  wire:model.lazy="hasta" class="form-control" style="font-size: .70rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-info" style="font-size: .70rem;">Bodega</h7>
                                            <div class="input-group input-group-sm" style="font-size: .70rem;">
                                                <select wire:model.lazy="bodega"   class="form-control text-center" id="tipodoc" style="font-size: .70rem;">  
                                                <option value="" style="font-size: .70rem;">Elegir</option>
                                                <option value="0" style="font-size: .70rem;">TODAS</option>
                                                @foreach($bodegas as $b)
                                                    <option value="{{$b->id}}" style="font-size: .70rem;">
                                                        {{$b->nombre}}
                                                    </option>                                       
                                                @endforeach                              
                                            </select>    
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="/rpconsumosumpers/'S',{{$desde}},{{$hasta}},{{$bodega}}" target="_blank"  style="font-size: .70rem;"
                                            data-toggle="tooltip" data-placement="top" title="Mandar a Impresor">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>                                    
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" wire:click="rpConsumoSumPer('{{$desde}}','{{$hasta}}','{{$bodega}}')"  style="font-size: .70rem;"
                                            data-toggle="tooltip" data-placement="top" title="Mandar a Excel">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>                                    
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-left" style="font-size: .70rem;">Consumos x Destino Detallado x Período</td>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-md-12 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-secondary" style="font-size: .70rem;">Desde</h7>
                                            <div  class="input-group input-group-sm ">
                                                <input type="date"  wire:model.lazy="desde" class="form-control" style="font-size: .70rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-12 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-secondary" style="font-size: .70rem;">Hasta</h7>
                                            <div  class="input-group input-group-sm ">
                                                <input type="date"  wire:model.lazy="hasta" class="form-control" style="font-size: .70rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-4 col-sm-12" style="font-size: .70rem;">
                                            <h7 class="text-info" style="font-size: .70rem;">Bodega</h7>
                                            <div class="input-group input-group-sm" style="font-size: .70rem;">
                                                <select wire:model.lazy="bodega"   class="form-control text-center" id="tipodoc" style="font-size: .70rem;">  
                                                <option value="" style="font-size: .70rem;">Elegir</option>
                                                <option value="0" style="font-size: .70rem;">TODAS</option>
                                                @foreach($bodegas as $b)
                                                    <option value="{{$b->id}}" style="font-size: .70rem;">
                                                        {{$b->nombre}}
                                                    </option>                                       
                                                @endforeach                              
                                            </select>    
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="/rpconsumodetpers/'S',{{$desde}},{{$hasta}},{{$bodega}}" target="_blank"  style="font-size: .70rem;"
                                            data-toggle="tooltip" data-placement="top" title="Mandar a Impresor">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>                                    
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" wire:click="rpConsumoDetPer('{{$desde}}','{{$hasta}}','{{$bodega}}')"  style="font-size: .70rem;"
                                            data-toggle="tooltip" data-placement="top" title="Mandar a Excel">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>                                    
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>

                        </tbody>
                        </table>
                    </div>
                </div>
        </div>
	</div>
</div>


@livewireScripts
<script type="text/javascript">

    datosproducto=[];
    datospaciente=[];
    regs=0;
    registro=[];
    var detalle=[];
    accion=1;
    editno=0;
    //variables totales documentos

    function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function Confirm(id)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿Seguro de Borrar Registro: ?',
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
        swal.close()
        })
    }


   document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('imprimir-auxiliar', idMov => {
        var wactual = window;
        var ruta = "{{url('auxiliar')}}" + '/' + idMov.mes + "," + idMov.ejercicio + "," +idMov.cuenta+","+idMov.nombrecuenta;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })

        window.livewire.on('giftEspera', dataMsg =>
        {
            $('#postear').html('<div class="loading text-center col-lg-12"><img src="images/loader.gif" alt="loading" /></div>');
        })

        window.livewire.on('finGiftEspera', dataMsg =>
        {
            div = document.getElementById('postear')
                div.style.display ='none'

        })





        window.livewire.on('msg-ok', dataMsg =>{ $('#modalCompraProd').modal('hide')})        



   })
   
</script>