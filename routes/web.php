<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ManifestController;
use App\Livewire\Conta\ContaEdit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tributacoes\Ncms\NcmIndex;
use App\Livewire\Admin\Empresas\EmpresaCreate;
use App\Livewire\Admin\Empresas\EmpresaIndex;
use App\Livewire\Admin\Permissions\PermissionIndex;
use App\Livewire\Admin\Roles\RoleIndex;
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

Route::get('/accept-invitation-tenant', [InvitationController::class, 'acceptInvitationTenantUser'])->name('aceitando.convite.empresa');
Route::get('/manifest.json', [ManifestController::class, 'manifest'])->name('manifest.json');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/minha-conta', ContaEdit::class)->name('conta.edit');

    Route::get('/empresas', EmpresaIndex::class)->name('empresas.index');
    Route::get('/empresa/create', EmpresaCreate::class)->name('empresa.create');

    Route::prefix('tributacoes')->group(function () {
        Route::get('/ncms', NcmIndex::class)->name('ncms.index');
        Route::get('/cests', CestIndex::class)->name('cests.index');
        Route::get('/cfops', CfopIndex::class)->name('cfops.index');
    });

    Route::get('/funcoes', RoleIndex::class)->name('funcoes.index');
    Route::get('/funcoes/permissoes', PermissionIndex::class)->name('permissoes.index');
    
    Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');

    Route::get('/manifest/generate', [ManifestController::class, 'generate'])->name('manifest.generate');
});

require __DIR__.'/auth.php';
