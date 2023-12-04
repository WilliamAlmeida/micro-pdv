<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produtos;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        if($request->filled('search')) {
            $produtos = Produtos::orderBy('titulo')->where('titulo', 'like', $request->search.'%')->limit(50)->get();
        }else{
            $produtos = Produtos::orderBy('titulo')->limit(50)->get();
        }

        return $produtos;
    }

    public function show(string $id)
    {
        return Produtos::find($id);
    }
}
