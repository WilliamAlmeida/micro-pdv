<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Clientes;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Clientes::orderBy('nome_fantasia');

        if($request->filled('search')) {
            $clientes->where('nome_fantasia', 'like', $request->search.'%');
        }

        if($request->filled('empresa')) $clientes->withTenant(1, $request->get('empresa'));

        return $clientes->limit(50)->get();
    }

    public function show(string $id)
    {
        return Clientes::find($id);
    }
}
