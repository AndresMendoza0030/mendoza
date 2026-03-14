<table class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
    <thead>
        <tr>
            <th class="text-center">ID Solicitud</th>
            <th>Usuario</th>
            <th>Concepto</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($solicitudes as $solicitud)
            <tr>
                <td class="text-center">{{ $solicitud->id }}</td>
                <td>{{ $solicitud->user->name }}</td>
                <td>{{ $solicitud->concepto }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($solicitud->fecha)->format('d-m-Y') }}</td>
                <td class="text-center">
                    @if ($solicitud->estatus == 0)
                        <span class="badge badge-warning">Pendiente</span>
                    @elseif($solicitud->estatus == 1)
                        <span class="badge badge-success">Aprobada</span>
                    @else
                        <span class="badge badge-danger">Desconocido</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('solicitudes.show', $solicitud->id) }}" class="btn btn-primary">Ver Detalle</a>
                    <a href="{{ route('solicitudes.pdf', $solicitud->id) }}" class="btn btn-secondary">Generar PDF</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Paginación -->
<div class="d-flex justify-content-between">
    <div>
        Mostrando {{ $solicitudes->firstItem() }} a {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }}
        registros.
    </div>
    <div>
        {{ $solicitudes->links() }}
    </div>
</div>
