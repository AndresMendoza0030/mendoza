@extends('layouts.template')
@section('title', 'Detalle Solicitud')

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <h5><b>Detalle de la Solicitud #{{ $solicitud->id }}</b></h5>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12 mt-2">

                        <div class="row">
                            <div class="col-md-4">
                                <p><b>Fecha:</b>
                                    {{ \Carbon\Carbon::parse($solicitud->fecha)->format('d-m-Y') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><b>Concepto:</b> {{ $solicitud->concepto }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><b>Bodega:</b>
                                    {{ optional($solicitud->warehouse)->nombre ?? $solicitud->bodega }}
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <p><b>Destino:</b>
                                    {{ optional($solicitud->destinoRel)->nombre ?? $solicitud->destino_id }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><b>Estado:</b>
                                    @if ($solicitud->estatus == 0)
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($solicitud->estatus == 1)
                                        <span class="badge badge-success">Aprobada</span>
                                    @else
                                        <span class="badge badge-danger">Desconocido</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h6><b>Productos Solicitados</b></h6>
                        @if ($solicitud->detalles->count() > 0)
                            <div class="table-responsive">
                                <table
                                    class="table table-sm table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
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
                            </div>
                        @else
                            <div class="alert alert-info">
                                Sin detalles registrados en esta solicitud.
                            </div>
                        @endif

                        <a href="{{ auth()->user()->hasRole('gerente') || auth()->user()->hasRole('admin') ? route('solicitudes.admin') : route('mis-solicitudes') }}"
                            class="btn btn-secondary mt-3">
                            Regresar a Solicitudes
                        </a>


                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
