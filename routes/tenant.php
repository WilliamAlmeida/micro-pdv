<?php

declare(strict_types=1);

use App\Livewire\Conta\ContaEdit;
use App\Livewire\Empresa\EmpresaEdit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tenant\Estoque\EstoqueIndex;
use App\Livewire\Tenant\Pdv\Caixa\CaixaIndex;
use App\Livewire\Tenant\Clientes\ClienteIndex;
use App\Livewire\Tenant\Pdv\Vendas\VendaIndex;
use App\Livewire\Tenant\Produtos\ProdutoIndex;
use App\Livewire\Tenant\Usuarios\UsuarioIndex;
use Livewire\Controllers\HttpConnectionHandler;
use App\Http\Middleware\InitializeTenancyByPath;
use App\Livewire\Tenant\Convenios\ConvenioIndex;
use App\Livewire\Tenant\Categorias\CategoriaIndex;
use App\Livewire\Tenant\Fornecedores\FornecedorIndex;
use App\Livewire\Tenant\Pdv\Fechamento\FechamentoIndex;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Livewire\Tenant\Pdv\Convenios\ConvenioIndex as PDVConvenioIndex;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Route::middleware([
//     'web',
//     InitializeTenancyByDomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     Route::get('/', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
// });

Route::middleware([
    'web', 'auth',
    InitializeTenancyByPath::class,
])->prefix('empresa/{tenant}')->name('tenant.')->group(function () {
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