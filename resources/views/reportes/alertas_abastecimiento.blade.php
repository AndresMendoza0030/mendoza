<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alertas de desabastecimiento</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }
        h1, p {
            margin: 0 0 8px 0;
        }
        .meta {
            margin-bottom: 14px;
        }
        .summary {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .summary td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th,
        table.data td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
        }
        table.data th {
            background: #f3f3f3;
        }
        .text-right {
            text-align: right;
        }
        .critico {
            color: #b10000;
            font-weight: bold;
        }
        .alto {
            color: #a66b00;
            font-weight: bold;
        }
        .small {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Alertas de desabastecimiento</h1>

    <div class="meta">
        <p><strong>Bodega:</strong> {{ $bodegaPrincipal->nombre }}</p>
        <p><strong>Fecha de generación:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>
        <p>
            <strong>Método:</strong>
            @if($config['modoAlerta'] === 'pedidos')
                Por pedidos restantes
            @elseif($config['modoAlerta'] === 'cobertura')
                Por cobertura
            @elseif($config['modoAlerta'] === 'minimo')
                Por stock mínimo
            @else
                Mixto
            @endif
        </p>
        <p class="small">
            Historial: {{ $config['meses'] }} meses |
            Excluir ajustes: {{ $config['excluirAjustes'] ? 'Sí' : 'No' }} |
            Mostrar por necesidad: {{ $config['mostrarPorNecesidad'] ? 'Sí' : 'No' }} |
            Umbral por necesidad: {{ $config['umbralNecesidad'] }}
        </p>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total de productos en alerta</strong><br>{{ $summary['total'] }}</td>
            <td><strong>Críticos</strong><br>{{ $summary['criticos'] }}</td>
            <td><strong>Altos</strong><br>{{ $summary['altos'] }}</td>
            <td><strong>Total sugerido a comprar</strong><br>{{ number_format($summary['comprar'], 0) }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Riesgo</th>
                <th class="text-right">Disponible</th>
                <th class="text-right">Consumo mes</th>
                <th class="text-right">Pedido típico</th>
                <th>Último pedido</th>
                <th class="text-right">Pedidos restantes</th>
                <th class="text-right">Cobertura</th>
                <th class="text-right">Compra sugerida</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r['id'] }}</td>
                    <td>{{ $r['nombre'] }}</td>
                    <td>{{ !empty($r['es_por_necesidad']) ? 'POR NECESIDAD' : 'ROTACION' }}</td>
                    <td>
                        @if($r['risk_level'] === 'CRITICO')
                            <span class="critico">CRÍTICO</span>
                        @elseif($r['risk_level'] === 'ALTO')
                            <span class="alto">ALTO</span>
                        @else
                            {{ $r['risk_level'] }}
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($r['disponible'], 0) }}</td>
                    <td class="text-right">{{ $r['consumo_mes'] }}</td>
                    <td class="text-right">{{ number_format($r['pedido_tipico'], 2) }}</td>
                    <td>{{ $r['ultimo_pedido'] ?: 'Sin registro' }}</td>
                    <td class="text-right">
                        @if($r['pedidos_restantes'] >= 999)
                            N/D
                        @else
                            {{ number_format($r['pedidos_restantes'], 2) }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($r['cobertura'] >= 999)
                            Sin consumo
                        @else
                            {{ number_format($r['cobertura'], 2) }}
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($r['compra_sugerida'], 0) }}</td>
                    <td>{{ $r['reason'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">No se encontraron productos en alerta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>