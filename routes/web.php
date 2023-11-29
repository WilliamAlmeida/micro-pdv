<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Empresa\EmpresaEdit;
use App\Livewire\Tributacoes\Cests\CestIndex;
use App\Livewire\Tributacoes\Cfops\CfopIndex;
use App\Livewire\Tributacoes\Ncms\NcmIndex;
use App\Livewire\Usuarios\UserIndex;
use Illuminate\Support\Facades\Route;

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
});

Route::prefix('painel')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    
        Route::get('/users', UserIndex::class)->name('users.index');

        Route::get('/empresa', EmpresaEdit::class)->name('empresa.edit');

        Route::prefix('tributacoes')->group(function () {
            Route::get('/ncms', NcmIndex::class)->name('ncms.index');
            Route::get('/cests', CestIndex::class)->name('cests.index');
            Route::get('/cfops', CfopIndex::class)->name('cfops.index');
        });
    });
});

require __DIR__.'/auth.php';
