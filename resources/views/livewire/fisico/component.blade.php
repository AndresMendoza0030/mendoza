<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">

    @if($action== 1)
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Movimientos de Conteo de Inventario (Físico)</b></h5>
                    </div> 
                </div>
            </div>
       		
            <!--div datos head documento -->
            <div class="widget-one">
                <div class="row justify-content-between mb-3 mt-2">
                    <div class="col-lg-4 col-md-12 col-sm-12 mb-1">
                        <div class="input-group input-group-sm ">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                            </div>
                            <input type="date" aria-label="Desde" class="form-control"  wire:model.lazy="desde" >
                            <input type="date" aria-label="Hasta" class="form-control"  wire:model.lazy="hasta" >
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-1 text-right">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                        </div>
                            <input type="text" wire:model="search" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
                        </div>
                    </div>

                    <div class="col-md-1 col-lg-2 col-sm-12 mt-1 text-right mb-1">
                        <button type="button" wire:click="doAction(2)" class="btn btn-sm btn-dark " data-toggle="tooltip" data-placement="top" title="Nuevo Conteo">
                            <i class="la la-file la-lg"></i>Nuevo Conteo
                        </button>
                    </div>
                </div>

                <div class="table-responsive-sm mt-2">
                    <table id="tabladetalle" name= "tabladetalle" class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                    <thead>
                        <tr>                                                   
                            <th class="text-center col-2">BODEGA</th>
                            <th class="text-center col-2">FECHA</th>
                            <th class="text-center col-3">CONCEPTO</th>
                            <th class="text-center col-1">ESTATUS</th>
                            <th class="text-center col-2">ACCIONES</th>                        
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($info)>0)
                            @foreach($info as $r)
                                <tr >

                                    <td class="text-left"><b>{{$r->id}} - {{$r->cbodega}}</b></td>
                                    <td class="text-center">{{$r->fecha}} - {{$r->usuario}}</td>
                                    <td class="text-left">{{$r->concepto}}</td>
                                    <td class="text-center"><b>
                                        @if($r->procesado=="S")
                                            >Procesado<
                                        @else
                                        *Pendiente*<a href="javascript:void(0);" wire:click="procesaConteo('{{$r->id}}')"
                                            data-toggle="tooltip" data-placement="top" title="Procesar Conteo">
                                            >></a>
                                        @endif
                                    </b></td>

                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);" wire:click="editarConteo('{{$r->id}}')"
                                                    data-toggle="tooltip" data-placement="top" title="Ver Detalle Ajuste">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="purple" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>                                                    
                                                </a>

                                                <a href="javascript:void(0);" onclick="Confirm('{{$r->id}}')"
                                                    data-toggle="tooltip" data-placement="top" title="Borrar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>

                                                <a href="javascript:void(0);" wire:click="imprimeConteo('{{$r->id}}')"
                                                    data-toggle="tooltip" data-placement="top" title="Imprimir Conteo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                                </a>


                                            </li>

                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{$info->links()}}
                </div>
            </div>
		</div>
    @else
        @include('livewire.fisico.form')    
    @endif
    @if($action==4)
        @include('livewire.fisico.formajuste')    
    @endif
    @include('livewire.fisico.modaledita')    
    @include('livewire.fisico.modalcrea')    
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

    //variables totales documentos

   function cambioEstado(id, prod,actual)
   {
    valor=$('#inp'+id).val();

    if (actual==valor)
    {
        return;
    }    
    window.livewire.emit('updConteo', valor, id)    
   }


   function updVence()
   {
    //
    if (Number($("#mdcantiante").val()) != Number($("#mdcantilote").val()))
    {
        elid = Number($("#mdloteid").val())
        elconteo=Number($("#mdcantilote").val())
        ladif=Number($("#mdcantiante").val())-elconteo

        if (ladif<0)
        {eltipo="E"}
        else
        {eltipo="S"}
        //
        $('#modaledita').modal('hide')    

        window.livewire.emit('updVencimiento', elid,elconteo,Math.abs(ladif),eltipo)
    }
    //
   }

   function agregarVence(reg1)
   //(xcodigo,xproducto,xlote,xcantidad,xid)
   {
    var infoo1= JSON.parse(reg1);
    var fecha = new Date();
    var ano = fecha.getFullYear();

    $("#mdcproducto").val(infoo1.producto)
    $("#mdccantiante").val(infoo1.producto_id)
    $("#mdccantilote").val('')
    $("#mdmesv").val('')
    $("#mdannov").val(ano)

    $('#modalcrea').modal('show')
    $("#mdccantilote").focus()
   }

   function grabarVence()
   {
    //
    if (Number($("#mdccantilote").val())>0)
    {
        elconteo=Number($("#mdccantilote").val())
        elmes=Number($("#mdmesv").val())
        elanno=Number($("#mdannov").val())
        elidprod=Number($("#mdccantiante").val())
        //
        $('#modalcrea').modal('hide')    
        if(elconteo>0 && elmes>0 && elanno>0 &&elidprod>0)
        {
            window.livewire.emit('grabaVencimiento', elconteo,elmes,elanno,elidprod)
        }
        else
        {
            toastr.error('Errores en las cantidad ignresadas...')
        }
    }
    else
    {
        toastr.error('Cantidad ignresada debe ser mayor a cero...')
    }
    //
   }

    function CheckNumeric()
	{
  		return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode == 46;
	}

    function muestraNumero()
    {
       swal(
        {
        title: 'Confirmación',
        text: 'Los Ajustes del Conteo han sido Grabados, revise navegador e imprima',
        type: 'success',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar',
        closeOnConfirm: true
        })
   }


   function Confirm(id)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR CONTEO No. '+id+" ?",
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
            window.livewire.emit('borrarConteo', id)    
            swal.close()
        })
   }

   function confirmaFin(id)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿SEGURO DE FINALIZAR CONTEO No. '+id+"?, AL HACERLO YA NO PODRA EDITAR",
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
            window.livewire.emit('grabarConteo',id)    
            swal.close()
        })
    }
  
    function descartarConteo(id)
    {
       swal(
        {
        title: 'CONFIRMAR',
        text: '¿SEGURO DE DESCARTAR CONTEO No. '+id+"?, AL HACERLO SE PERDERA TODO EL CONTEO",
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
            window.livewire.emit('descartarConteo',id)
            swal.close()
        })
    }

    document.addEventListener('DOMContentLoaded', function()
   {
        window.livewire.on('print-mov', idMov => {
        var wactual = window;
        var ruta = "{{url('detallemov')}}" + '/' + idMov;
        var w = window.open(ruta, "_blank");
        wactual.focus();
        })

        window.livewire.on('mascaranumero', Masca =>{
         $('.numero').mask("#,##0.00", {reverse: true});
        })

        window.livewire.on('confirmaOrden',idord=>
        {
            muestraNumero();
        })

        window.livewire.on('printconteo', evento =>
        {
        var wactual = window;
        var ruta = "{{url('invfisico')}}" +'/'+evento.idmov;
        var w = window.open(ruta, "_blank");
        w.document.title="Conteo No. ";
        wactual.focus();
        })

        window.livewire.on('printconteovence', evento =>
        {
        var wactual = window;
        var ruta = "{{url('vencefisico')}}" +'/'+evento.idmov;
        var w = window.open(ruta, "_blank");
        w.document.title="Conteo No. ";
        wactual.focus();
        })
   
   })
   
</script>