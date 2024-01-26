<?php

use App\Livewire\Conta\ContaEdit;
use App\Livewire\Empresa\EmpresaEdit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tributacoes\Ncms\NcmIndex;
use App\Livewire\Admin\Usuarios\UsuarioIndex;
use App\Livewire\Tributacoes\Cests\CestIndex;
use App\Livewire\Tributacoes\Cfops\CfopIndex;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/minha-conta', ContaEdit::class)->name('conta.edit');

    Route::get('/empresa', EmpresaEdit::class)->name('empresa.edit');

    Route::prefix('tributacoes')->group(function () {
        Route::get('/ncms', NcmIndex::class)->name('ncms.index');
        Route::get('/cests', CestIndex::class)->name('cests.index');
        Route::get('/cfops', CfopIndex::class)->name('cfops.index');
    });
    
    Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');
});

require __DIR__.'/auth.php';
