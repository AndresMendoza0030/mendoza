<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Read-only server-to-server adapter. The existing /api/user route is unchanged.
Route::prefix('workspace-inventory')->middleware('workspace.inventory')->group(function () {
    Route::get('bootstrap', 'WorkspaceInventoryController@bootstrap');
    Route::get('products', 'WorkspaceInventoryController@products');
    Route::get('stocks', 'WorkspaceInventoryController@stocks');
    Route::get('movements', 'WorkspaceInventoryController@movements');
    Route::get('requests', 'WorkspaceInventoryController@requests');
});
