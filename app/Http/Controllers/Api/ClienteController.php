<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if($request->filled('search')) {
            $clientes = Clientes::orderBy('nome_fantasia')
            ->where('nome_fantasia', 'like', $request->search.'%')
            ->limit(50)->get();
        }else{
            $clientes = Clientes::orderBy('nome_fantasia')->limit(50)->get();
        }

        return $clientes;
    }

    public function show(string $id)
    {
        return Clientes::find($id);
    }
}
