<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle Solicitud #{{ $solicitud->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        h5,
        h6 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h5><b>Detalle de la Solicitud #{{ $solicitud->id }}</b></h5>

    <div>
        <p><b>Fecha:</b> {{ \Carbon\Carbon::parse($solicitud->fecha)->format('d-m-Y') }}</p>
        <p><b>Concepto:</b> {{ $solicitud->concepto }}</p>
        <p><b>Bodega:</b> {{ optional($solicitud->warehouse)->nombre ?? $solicitud->bodega }}</p>
        <p><b>Destino:</b> {{ optional($solicitud->destinoRel)->nombre ?? $solicitud->destino_id }}</p>
        <p><b>Estado:</b>
            @if ($solicitud->estatus == 0)
                <span>Pendiente</span>
            @elseif($solicitud->estatus == 1)
                <span>Aprobada</span>
            @else
                <span>Desconocido</span>
            @endif
        </p>
    </div>

    <hr>

    <h6><b>Productos Solicitados</b></h6>
    @if ($solicitud->detalles->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">ID Prod</th>
                    <th>Producto</th>
                    <th class="text-center">Cant. Solicitada</th>
                    <th class="text-center">Cant. Autorizada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitud->detalles as $item)
                    <tr>
                        <td class="text-center">{{ $item->producto_id }}</td>
                        <td>{{ $item->producto }}</td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-center">{{ $item->autorizado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Sin detalles registrados en esta solicitud.</p>
    @endif

</body>

</html>
