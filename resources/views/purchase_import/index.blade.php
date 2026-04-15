@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Captura de factura</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('purchase-import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Foto de factura</label>
                    <input type="file" name="invoice_image" class="form-control" accept="image/*" capture="environment" required>
                </div>
                <button class="btn btn-primary">Procesar OCR</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Facturas capturadas</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($imports as $import)
                        <tr>
                            <td>{{ $import->id }}</td>
                            <td>{{ $import->supplier_name ?: 'Sin proveedor' }}</td>
                            <td>{{ $import->document_number ?: 'Sin numero' }}</td>
                            <td>{{ optional($import->issue_date)->format('d/m/Y') }}</td>
                            <td>${{ number_format((float) $import->total, 2) }}</td>
                            <td>{{ $import->status }}</td>
                            <td>
                                <a href="{{ route('purchase-import.review', $import->id) }}" class="btn btn-sm btn-outline-primary">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay facturas cargadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $imports->links() }}
    </div>
</div>
@endsection
