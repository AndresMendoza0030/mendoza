@extends('layouts.template')
@section('title', 'Mis Solicitudes')

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <h5><b>Mis Solicitudes de Gasto</b></h5>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12 mt-2">

                        @if ($solicitudes->count() == 0)
                            <div class="alert alert-info">
                                <strong>Info:</strong> No tienes solicitudes registradas.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table
                                    class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Concepto</th>
                                            <th class="text-center">Bodega</th>
                                            <th class="text-center">Destino</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($solicitudes as $sol)
                                            <tr>
                                                <td class="text-center">{{ $sol->id }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($sol->fecha)->format('d-m-Y') }}
                                                </td>
                                                <td>{{ $sol->concepto }}</td>
                                                <td class="text-center">
                                                    {{ optional($sol->warehouse)->nombre ?? $sol->bodega }}
                                                </td>
                                                <td class="text-center">
                                                    {{ optional($sol->destinoRel)->nombre ?? $sol->destino_id }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($sol->estatus == 0)
                                                        <span class="badge badge-warning">Pendiente</span>
                                                    @elseif($sol->estatus == 1)
                                                        <span class="badge badge-success">Aprobada</span>
                                                    @else
                                                        <span class="badge badge-danger">Despachado</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('solicitudes.show', $sol->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Ver Detalle
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
