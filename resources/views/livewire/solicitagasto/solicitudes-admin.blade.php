@extends('layouts.template')

@section('title', 'Todas las Solicitudes')

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <h5><b>Solicitudes de Todos los Usuarios</b></h5>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12 mt-2">
                        <form method="GET" action="{{ route('solicitudes.admin') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Buscar por ID o Usuario..." value="{{ old('search', $search) }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Buscar</button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $startDate) }}" placeholder="Fecha inicio">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ old('end_date', $endDate) }}" placeholder="Fecha fin">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary" type="submit">Filtrar por fechas</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla con todas las solicitudes -->
                <div class="row mt-1">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12 mt-2">

                        <div class="table-responsive">
                            <table
                                class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
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
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($solicitud->fecha)->format('d-m-Y') }}</td>
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
                                                <a href="{{ route('solicitudes.show', $solicitud->id) }}"
                                                    class="btn btn-primary">Ver Detalle</a>
                                                <a href="{{ route('solicitudes.pdf', $solicitud->id) }}"
                                                    class="btn btn-secondary" target="_blank">Generar PDF</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between">
                            <div>
                                Mostrando {{ $solicitudes->firstItem() }} a {{ $solicitudes->lastItem() }} de
                                {{ $solicitudes->total() }} registros.
                            </div>
                            <div>
                                {{ $solicitudes->links() }} <!-- Paginación de Laravel -->
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
