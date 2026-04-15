@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Revision de factura</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-header">Resumen OCR</div>
                <div class="card-body">
                    <div><strong>Proveedor:</strong> {{ $purchaseImport->supplier_name ?: 'No detectado' }}</div>
                    <div><strong>NIT:</strong> {{ $purchaseImport->supplier_nit ?: 'No detectado' }}</div>
                    <div><strong>NRC:</strong> {{ $purchaseImport->supplier_nrc ?: 'No detectado' }}</div>
                    <div><strong>Documento:</strong> {{ $purchaseImport->document_number ?: 'No detectado' }}</div>
                    <div><strong>Fecha:</strong> {{ optional($purchaseImport->issue_date)->format('d/m/Y') ?: 'No detectada' }}</div>
                    <div><strong>Total:</strong> ${{ number_format((float) $purchaseImport->total, 2) }}</div>
                    <div><strong>Estado:</strong> {{ $purchaseImport->status }}</div>
                </div>
            </div>

            @if($purchaseImport->image_path)
                <div class="card mb-3">
                    <div class="card-header">Imagen</div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $purchaseImport->image_path) }}" class="img-fluid rounded" alt="Factura">
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">Texto OCR</div>
                <div class="card-body">
                    <pre class="mb-0 small" style="white-space: pre-wrap;">{{ $purchaseImport->raw_text }}</pre>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form action="{{ route('purchase-import.confirm', $purchaseImport->id) }}" method="POST">
                @csrf

                <div class="card mb-3">
                    <div class="card-header">Datos para registrar la compra</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="">-- Seleccionar proveedor --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ (string) old('supplier_id', $selectedSupplierId) === (string) $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->razon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bodega</label>
                                <select class="form-select" name="warehouse_id" required>
                                    <option value="">-- Seleccionar bodega --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id', $selectedWarehouseId) === (string) $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($suppliers->isEmpty() || $warehouses->isEmpty())
                            <div class="alert alert-warning mb-0">
                                Debes tener al menos un proveedor y una bodega activos para confirmar la compra.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Detalle detectado</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descripcion OCR</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario</th>
                                        <th>Producto sugerido</th>
                                        <th>Score</th>
                                        <th>Revision</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseImport->items as $i => $item)
                                        <tr>
                                            <td>
                                                {{ $item->line_no }}
                                                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                            </td>
                                            <td>{{ $item->extracted_description }}</td>
                                            <td>
                                                <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" name="items[{{ $i }}][qty]" value="{{ old('items.' . $i . '.qty', $item->extracted_qty) }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.0001" min="0" class="form-control form-control-sm" name="items[{{ $i }}][unit_price]" value="{{ old('items.' . $i . '.unit_price', $item->extracted_unit_price) }}">
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" name="items[{{ $i }}][matched_product_id]">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ (string) old('items.' . $i . '.matched_product_id', $item->matched_product_id) === (string) $product->id ? 'selected' : '' }}>
                                                            {{ $product->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{{ $item->match_score }}</td>
                                            <td>
                                                @if($item->needs_review)
                                                    <span class="badge bg-warning text-dark">Revisar</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No se detectaron lineas de producto en la factura.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('purchase-import.index') }}" class="btn btn-secondary">Volver</a>
                    <button type="submit" class="btn btn-primary" {{ $suppliers->isEmpty() || $warehouses->isEmpty() ? 'disabled' : '' }}>
                        Confirmar entrada por compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if(false)
                    {{ $purchaseImport->raw_text }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('purchase-import.confirm', $purchaseImport->id) }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-header">Detalle detectado</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción OCR</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Producto sugerido</th>
                                <th>Score</th>
                                <th>Revisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseImport->items as $i => $item)
                                <tr>
                                    <td>
                                        {{ $item->line_no }}
                                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td>{{ $item->extracted_description }}</td>
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" name="items[{{ $i }}][qty]" value="{{ $item->extracted_qty }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" class="form-control form-control-sm" name="items[{{ $i }}][unit_price]" value="{{ $item->extracted_unit_price }}">
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="items[{{ $i }}][matched_product_id]">
                                            <option value="">-- Seleccionar --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ (string)$item->matched_product_id === (string)$product->id ? 'selected' : '' }}>
                                                    {{ $product->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $item->match_score }}</td>
                                    <td>
                                        @if($item->needs_review)
                                            <span class="badge bg-warning text-dark">Revisar</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('purchase-import.index') }}" class="btn btn-secondary">Volver</a>
            <button type="submit" class="btn btn-primary">Confirmar entrada por compra</button>
        </div>
    </form>
</div>
@endif
