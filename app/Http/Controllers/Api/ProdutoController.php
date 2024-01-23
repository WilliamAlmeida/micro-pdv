<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Produtos;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $produtos = Produtos::orderBy('titulo');

        if($request->filled('search')) {
            $produtos->where('titulo', 'like', $request->search.'%');
        }

        if($request->filled('empresa')) $produtos->withTenant(1, $request->get('empresa'));

        return $produtos->limit(50)->get();
    }

    public function show(string $id)
    {
        return Produtos::find($id);
    }
}
