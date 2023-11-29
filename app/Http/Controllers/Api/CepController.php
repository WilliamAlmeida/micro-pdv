<?php

namespace App\Http\Controllers\Api;

use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CepController extends Controller
{
    public function show(string $cep)
    {
        $cep = preg_replace( '/[^0-9]/', '', $cep);

        if(strlen($cep) != 8) return [];

        /* Garantir que seja lido sem problemas */
        header("Content-Type: text/plain");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/".$cep."/json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $retorno = curl_exec($ch);
        curl_close($ch);

        $retorno = json_decode($retorno);

        $retorno->idestado = null;
        $retorno->idcidade = null;

        if(isset($retorno->uf)) {
            $estado = Estado::whereUf($retorno->uf)->first();
            if($estado) {
                $retorno->idestado      = $estado->id;
                unset($estado);
            }
        }

        if(isset($retorno->localidade)) {
            $cidade = Cidade::whereNome($retorno->localidade)->first();
            if($cidade) {
                $retorno->idcidade      = $cidade->id;
                unset($cidade);
            }
        }

        return json_encode($retorno, JSON_PRETTY_PRINT);
    }
}
