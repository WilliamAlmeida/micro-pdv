<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Categorias\CategoriaIndex;
use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Convenios\ConvenioIndex;
use App\Livewire\Empresa\EmpresaEdit;
use App\Livewire\Estoque\EstoqueIndex;
use App\Livewire\Fornecedores\FornecedorIndex;
use App\Livewire\Produtos\ProdutoIndex;
use App\Livewire\Tributacoes\Cests\CestIndex;
use App\Livewire\Tributacoes\Cfops\CfopIndex;
use App\Livewire\Tributacoes\Ncms\NcmIndex;
use App\Livewire\Usuarios\UsuarioIndex;
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
    
        Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');

        Route::get('/empresa', EmpresaEdit::class)->name('empresa.edit');
        Route::get('/categorias', CategoriaIndex::class)->name('categorias.index');
        
        Route::get('/estoque', EstoqueIndex::class)->name('estoque.index');
        Route::get('/produtos', ProdutoIndex::class)->name('produtos.index');

        Route::get('/fornecedores', FornecedorIndex::class)->name('fornecedores.index');
        Route::get('/convenios', ConvenioIndex::class)->name('convenios.index');
        Route::get('/clientes', ClienteIndex::class)->name('clientes.index');

        Route::prefix('tributacoes')->group(function () {
            Route::get('/ncms', NcmIndex::class)->name('ncms.index');
            Route::get('/cests', CestIndex::class)->name('cests.index');
            Route::get('/cfops', CfopIndex::class)->name('cfops.index');
        });
    });
});

require __DIR__.'/auth.php';
