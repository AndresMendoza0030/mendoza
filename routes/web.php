<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SolicitagastoController;
use App\Http\Controllers\PurchaseImportController;

Route::get('/', function () {
    return view('auth.login');
});

Route::match(['get', 'post'], 'register', function () {
    return view('auth.register');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::middleware(['auth'])->group(function () {

    Route::view('/home', 'dash');
    Route::view('dash', 'dash');

    //Route::group(['middleware' => ['role_or_permission:hospital|NIVEL ENFERMERIA|QUIROFANOS']], function () {
    Route::view('classes', 'classes')->middleware('role_or_permission:gerente');
    Route::view('lines', 'lines')->middleware('role_or_permission:gerente');
    Route::view('manufacturers', 'manufacturers')->middleware('role_or_permission:gerente');
    Route::view('productclassinvoices', 'productclassinvoices')->middleware('role_or_permission:gerente');

    Route::view('warehouses', 'warehouses')->middleware('role_or_permission:gerente');
    Route::view('destinos', 'destinos')->middleware('role_or_permission:gerente');

    Route::view('fisico', 'fisico')->middleware('role_or_permission:gerente|bodega');

    Route::view('products', 'products')->middleware('role_or_permission:gerente|bodega');
    Route::view('clientes', 'clientes')->middleware('role_or_permission:gerente|bodega');
    Route::view('proveedores', 'proveedores')->middleware('role_or_permission:gerente|bodega');

    Route::view('compras', 'compras')->middleware('role_or_permission:gerente|bodega');
    Route::view('traslados', 'traslados')->middleware('role_or_permission:gerente|bodega');
    Route::view('ajustes', 'ajustes')->middleware('role_or_permission:gerente|bodega');
    Route::view('gastos', 'gastos')->middleware('role_or_permission:gerente|bodega');
    Route::view('movimientos', 'movimientos')->middleware('role_or_permission:gerente|bodega');

    // Operaciones caja
    Route::view('solicitastock', 'solicitastock')->middleware('role_or_permission:gerente|bodega|solicitante');
    Route::view('solicitagasto', 'solicitagasto')->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::view('visordespacho', 'visordespacho')->middleware('role_or_permission:gerente|bodega');
    Route::view('autorizasol', 'autorizadespacho')->middleware('role_or_permission:gerente');

    Route::view('permisos', 'permisos')->middleware('role_or_permission:gerente');

    Route::view('clave', 'clave');

    Route::view('controlreportes', 'controlreportes')->middleware('role_or_permission:gerente|bodega');
    Route::view('controlsolicitante', 'controlsolicitante')->middleware('role_or_permission:gerente|solicitante');

    ////////
    Route::get('/detallemov/{iddocu}', 'Pdfcontroller@PDFdetalleMovimiento')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::get('/rpvence/{bd},{me},{an}', 'PdfContacontroller@PDFVence')
        ->middleware('role_or_permission:gerente|bodega|solicitante');
    Route::get('/rpexistencia/{bd},{fe}', 'PdfRpExistencia@PDFExistencia')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::get('/cataprod', 'PdfRpListaProducto@PDFCatalogo')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::get('/rpcomprasumaper/{desde},{hasta},{bodega}', 'PdfRpComprasPeriodoSum@PDFCompraPeriodoZ')
        ->middleware('role_or_permission:gerente|bodega');
    Route::get('/rpcompradetaper/{desde},{hasta},{bodega}', 'PdfRpComprasPeriodoDet@PDFCompraPeriodoX')
        ->middleware('role_or_permission:gerente|bodega');

    Route::get('/rpconsumosumper/{desde},{hasta},{bodega}', 'PdfRpConsumoPeriodoSum@PDFConsumoPeriodoZ')
        ->middleware('role_or_permission:gerente|bodega');
    Route::get('/rpconsumodetper/{desde},{hasta},{bodega}', 'PdfRpConsumoPeriodoDet@PDFConsumoPeriodoX')
        ->middleware('role_or_permission:gerente|bodega');

    Route::get('/rpconsumosumpers/{tipo},{desde},{hasta},{bodega}', 'PdfRpConsumoPeriodoSum@PDFConsumoPeriodoZS')
        ->middleware('role_or_permission:gerente|solicitante');
    Route::get('/rpconsumodetpers/{tipo},{desde},{hasta},{bodega}', 'PdfRpConsumoPeriodoDet@PDFConsumoPeriodoXS')
        ->middleware('role_or_permission:gerente|solicitante');

    Route::get('/infoprod/{id}', 'ComprasController@infoproducto')
        ->middleware('role_or_permission:gerente');
    Route::get('/stock/{id},{bd}', 'ComprasController@productStock')
        ->middleware('role_or_permission:gerente');
    Route::get('/lotesv/{id},{bd}', 'ComprasController@lotesBodega')
        ->middleware('role_or_permission:gerente');
    Route::get('/existedoc/{tp},{sr},{cr}', 'ComprasController@existeDocVenta')
        ->middleware('role_or_permission:gerente');

    // ===========================
    // NUEVAS RUTAS de SOLICITUDES
    // ===========================
    Route::get('/mis-solicitudes', [SolicitagastoController::class, 'verMisSolicitudes'])
        ->name('mis-solicitudes')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::get('/mis-solicitudes/{id}', [SolicitagastoController::class, 'showSolicitud'])
        ->name('solicitudes.show')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    Route::get('/pdf/gasto/{idmov}', 'PDFGastoController@generatePDF')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    // Ver todas las solicitudes de todos los usuarios
    Route::get('/solicitudes/admin', [SolicitagastoController::class, 'verSolicitudesAdmin'])
        ->name('solicitudes.admin')
        ->middleware('role_or_permission:gerente');

    // Ver detalle de una solicitud específica
    Route::get('/solicitudes/{id}', [SolicitagastoController::class, 'showSolicitud'])
        ->name('solicitudes.show')
        ->middleware('role_or_permission:gerente|bodega|solicitante');

    // Generar PDF de una solicitud específica
    Route::get('/pdf/solicitud/{id}', [SolicitagastoController::class, 'generarPDFSolicitud'])
        ->name('solicitudes.pdf')
        ->middleware('role_or_permission:gerente');
    Route::get('/inventario/consumo-producto', function () {
     return view('consumo_producto');
        })->name('inventario.consumo-producto')->middleware('role_or_permission:gerente|bodega');
    // Ruta para generar PDF de todas las solicitudes (esto depende de tu flujo)
    Route::get('/pdf/solicitudes', [SolicitagastoController::class, 'generarPDFSolicitudes'])
        ->name('solicitudes.pdf.total')
        ->middleware('role_or_permission:gerente');
    Route::get('/inventario/alertas/pdf', 'PdfAlertasAbastecimiento@PDFAlertas')
        ->name('inventario.alertas.pdf')
        ->middleware('role_or_permission:gerente|bodega');
    Route::prefix('compras/captura')->middleware('role_or_permission:gerente|bodega')->group(function () {
        Route::get('/', [PurchaseImportController::class, 'index'])->name('purchase-import.index');
        Route::post('/upload', [PurchaseImportController::class, 'upload'])->name('purchase-import.upload');
        Route::get('/{id}/review', [PurchaseImportController::class, 'review'])->name('purchase-import.review');
        Route::post('/{id}/confirm', [PurchaseImportController::class, 'confirm'])->name('purchase-import.confirm');
    });
    Route::get('/inventario/alertas', function () {
    return view('alertas_inteligentes');
    })->name('inventario.alertas');
});
