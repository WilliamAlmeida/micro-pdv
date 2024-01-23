<?php

use App\Livewire\Conta\ContaEdit;

use App\Livewire\Empresa\EmpresaEdit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tributacoes\Ncms\NcmIndex;
use App\Livewire\Admin\Usuarios\UsuarioIndex;
use App\Livewire\Tenant\Estoque\EstoqueIndex;
use App\Livewire\Tenant\Pdv\Caixa\CaixaIndex;
use App\Livewire\Tributacoes\Cests\CestIndex;
use App\Livewire\Tributacoes\Cfops\CfopIndex;
use App\Livewire\Tenant\Clientes\ClienteIndex;
use App\Livewire\Tenant\Pdv\Vendas\VendaIndex;
use App\Livewire\Tenant\Produtos\ProdutoIndex;
use App\Livewire\Tenant\Convenios\ConvenioIndex;
use App\Livewire\Tenant\Categorias\CategoriaIndex;
use App\Livewire\Tenant\Fornecedores\FornecedorIndex;
use App\Livewire\Tenant\Pdv\Fechamento\FechamentoIndex;
use App\Livewire\Tenant\Pdv\Convenios\ConvenioIndex as PDVConvenioIndex;

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

Route::middleware('auth')->prefix('painel')->name('tenant.')->group(function () {
    Route::get('/dashboard', function () {
        return view('tenant.dashboard');
    })->name('dashboard');

    Route::get('/minha-conta', ContaEdit::class)->name('conta.edit');
    
    Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');
    
    Route::get('/empresa', EmpresaEdit::class)->name('empresa.edit');
    Route::get('/categorias', CategoriaIndex::class)->name('categorias.index');
    
    Route::get('/estoque', EstoqueIndex::class)->name('estoque.index');
    Route::get('/produtos', ProdutoIndex::class)->name('produtos.index');
    
    Route::get('/fornecedores', FornecedorIndex::class)->name('fornecedores.index');
    Route::get('/convenios', ConvenioIndex::class)->name('convenios.index');
    Route::get('/clientes', ClienteIndex::class)->name('clientes.index');
    
    Route::prefix('pdv')->group(function () {
        Route::get('/', CaixaIndex::class)->name('pdv.index');
        
        Route::get('vendas', VendaIndex::class)->name('pdv.vendas');
        Route::get('convenios', PDVConvenioIndex::class)->name('pdv.convenios');
        Route::get('fechamento', FechamentoIndex::class)->name('pdv.fechamento');
    });
});

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
