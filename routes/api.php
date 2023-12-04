<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CepController;
use App\Http\Controllers\Api\NcmController;
use App\Http\Controllers\Api\CnpjController;
use App\Http\Controllers\Api\ProdutoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('tributacoes/ncms', [NcmController::class, 'index'])->name('api.ncms');
Route::get('tributacoes/ncms/{id}', [NcmController::class, 'show'])->name('api.ncms.show');

Route::get('produtos', [ProdutoController::class, 'index'])->name('api.produtos');

Route::get('cep/{cep}', [CepController::class, 'show'])->name('api.cep.show');
Route::get('cnpj/{cnpj}', [CnpjController::class, 'show'])->name('api.cnpj.show');