<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    <title>Detalle Cuenta Paciente</title>
    <!-- Styles -->
    <link href="css/app.css" rel="stylesheet">
    <link href="css/print.css" rel="stylesheet">
</head>

<body>
<main>
    <p style="font-size: 11pt; padding:2px; font-family:'Source Sans Pro';"><strong>Clínica Especialidades Santa Fé</strong></p>
    <p style="font-size: 11pt; padding:2px; font-family:'Source Sans Pro';"><strong>Detalle Cuenta Paciente</strong></p> 
        @foreach($datospaciente as $dt)
        <table class="table table-bordered table-sm "
         style="width: 100%; margin-top: 5px; border-bottom: 1px solid grey; margin-bottom: 5px;" border="0" cellspacing="0" cellpadding="5">
            <thead>
                <tr>                                                   
                    <th class="text-center" style="font-size: 0.8em; padding: 0.07em;" scope="col">Id Paciente</th>
                    <th class="text-center" style="font-size: 0.8em; padding: 0.07em;" scope="col">Nombre Paciente </th>
                    <th class="text-center" style="font-size: 0.8em; padding: 0.07em;" scope="col">Habitación</th>
                    <th class="text-center" style="font-size: 0.8em; padding: 0.07em;" scope="col">Médico </th>
                    <th class="text-center" style="font-size: 0.8em; padding: 0.07em;" scope="col">Fecha Ingreso</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" scope="row">{{$dt->id}}</th>
                    <td  class="text-center">{{$dt->nombrepaciente}}</td>                     
                    <td  class="text-center">{{$dt->elcuarto}}</td>                     
                    <td class="text-center">{{$dt->elmedico}}</td>                     
                    <td class="text-center">{{$dt->fecha}} - {{$dt->hora}}</td>                     
                </tr>
            </tbody>
        </table>
        @endforeach

    @if(count($cargos) > 0)
    <table class="table table-sm ">
        <thead class="thead-light">
            <tr style="font-size: 0.8em; padding: 0.05em;">                                                   
                <th  scope="col" class="text-center">Clase</th>
                <th  scope="col" class="text-center">Fecha</th>
                <th  scope="col" class="text-center">Cantidad</th>
                <th  scope="col" class="text-center">Producto/Servicio</th>
                <th  scope="col" class="text-center">MontoVenta</th>
                <th  scope="col" class="text-center">Referencia</th>
            </tr>
        </thead>
        @foreach($cargos as $r)
            <tbody>
                <tr scope="row"  style="font-size: 0.8em; padding: 0.05em;">
                    <td >{{$r->nclasef}}</td>
                    <td  class="text-center">{{$r->fecha}}</td>
                    <td  class="text-center">
                            @if($r->movimiento=="E")
                                ({{$r->cantidad}})
                            @else
                                {{$r->cantidad}}
                            @endif
                    </td>
                    <td >{{$r->producto_id}}- {{$r->producto}}</td>
                    <td class="text-center">{{$r->total}}</td>
                    <td >{{$r->tipodoc}}-{{$r->numerodoc}} {{$r->cbodega}}</td>
                </tr>
        @endforeach
        @foreach($cargosadmon as $r2)
            @if($r2->totadm>0)
                <tr scope="row"  style="font-size: 0.8em; padding: 0.07em;">
                    <td>{{$r2->clasef}}</td>
                    <td>{{date('Y-m-d')}}</td>
                    <td class="text-center">
                                {{1}}
                    </td>
                    <td>{{$r2->clasef}}</td>
                    <td class="text-center">{{$r2->totadm}}</td>
                    <td></td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <table  class="table table-bordered table-sm">
        <thead>
            <tr>                                                   
                <th >RESUMEN DE CUENTA</th>
            </tr>

            <tr>                                                   
                <th class="text-center">CONCEPTO DE SERVICIO</th>
                <th class="text-center">TOTAL</th>
            </tr>
        </thead>
        {{$tt=0}}
        <tbody>
            @foreach($resumen as $rex)
                <tr>
                    <td class="text-center">{{$rex->clasef}}</td>
                    <td class="text-right">{{$rex->total}}</td>
                </tr>
                {{$tt=$tt + $rex->total}}
            @endforeach
        </tbody>
        <tfoot>
            <tr>                                                   
                <th class="text-center">TOTAL DE LA CUENTA</th>
                <th class="text-right">{{$tt}}</th>
            </tr>
        </thead>
    </table>
    @endif
</main>
<footer>
</footer>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Source-sans-Pro,Arial, Helvetica, sans-serif", "normal");
                $pdf->text(70, 820, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 9);
            ');
        }
    </script>
</body>
</html>
