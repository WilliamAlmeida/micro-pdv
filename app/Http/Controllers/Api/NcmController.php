<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tributacoes\Ncm;
use Illuminate\Http\Request;

class NcmController extends Controller
{
    public function index(Request $request)
    {
        if($request->filled('search')) {
            $ncms = Ncm::orderBy('ncm')->where('ncm', 'like', $request->search.'%')->limit(50)->get();
        }else{
            $ncms = Ncm::orderBy('ncm')->limit(50)->get();
        }

        return $ncms;
    }

    public function show(string $id)
    {
        return Ncm::find($id);
    }
}
