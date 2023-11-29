<?php

namespace App\Http\Controllers\Api;

use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CnpjController extends Controller
{
    public function show(string $cnpj) {
        $cnpj = preg_replace( '/[^0-9]/', '', $cnpj);

        if(strlen($cnpj) != 14) return [];

        /* Garantir que seja lido sem problemas */
        header("Content-Type: text/plain");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.receitaws.com.br/v1/cnpj/".$cnpj);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $retorno = curl_exec($ch);
        curl_close($ch);

        $retorno = json_decode($retorno);

        $retorno->end_cep           = $retorno->cep ?? '';
        $retorno->end_logradouro    = $retorno->logradouro ?? '';
        $retorno->end_bairro        = $retorno->bairro ?? '';
        $retorno->end_numero        = $retorno->numero ?? '';

        $retorno->idestado = null;
        $retorno->idcidade = null;

        if(isset($retorno->uf)) {
            $estado = Estado::whereUf($retorno->uf)->first();
            if($estado) {
                $retorno->idestado      = $estado->id;
                unset($estado);
            }
        }

        if(isset($retorno->municipio)) {
            $cidade = Cidade::whereNome($retorno->municipio)->first();
            if($cidade) {
                $retorno->idcidade      = $cidade->id;
                unset($cidade);
            }
        }

        $retorno->end_cidade        = $retorno->municipio ?? '';
        $retorno->end_complemento   = $retorno->complemento ?? '';
        $retorno->razao_social      = $retorno->nome ?? '';
        $retorno->nome_fantasia     = (empty($retorno->fantasia)) ? $retorno->razao_social : $retorno->fantasia;

        return json_encode($retorno, JSON_PRETTY_PRINT);
    }
}
