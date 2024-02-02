<?php

namespace App\Http\Controllers\Api;

use App\Models\Cidade;
use App\Models\Estado;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CnpjController extends Controller
{
    public function show(string $cnpj) {
        $cnpj = preg_replace( '/[^0-9]/', '', $cnpj);

        if(strlen($cnpj) != 14) return json_encode(['status' => 'ERROR', 'message' => 'Cnpj invalido.', 'status_code' => 400]);

        if(!cache()->has('api.cnpj.'.$cnpj)) {
            if(cache()->has('api.cnpj.request_limited')) {
                return cache()->get('api.cnpj.request_limited');
            }
    
            $response = Http::get("http://www.receitaws.com.br/v1/cnpj/".$cnpj);
    
            if(!$response->successful()) {
                return cache()->remember('api.cnpj.request_limited', 60, function () use ($response) {
                    if($response->status() == 429) {
                        return json_encode(['status' => 'ERROR', 'message' => 'Muitas requisições. Por favor tente após 1 minuto.', 'status_code' => $response->status()]);
                    }else{
                        return json_encode(['status' => 'ERROR', 'message' => 'Falha ao buscar o cnpj. Por favor tente após 1 minuto', 'status_code' => $response->status()]);
                    }
                });
            }
    
            $retorno = collect($response->json());
    
            if($retorno->has('status') && $retorno->get('status') == 'ERROR') {
                return cache()->remember('api.cnpj.'.$cnpj, 60 * 5, function () use ($retorno) {
                    return json_encode($retorno->all());
                });
            }
    
            $retorno = cache()->remember('api.cnpj.'.$cnpj, 60 * 5, function () use ($retorno) {
                $retorno->put('idestado', null);
                $retorno->put('idcidade', null);
                $retorno->put('idpais', null);

                if($retorno->get('uf')) {
                    $estado = Estado::whereUf($retorno->get('uf'))->first();
                    if($estado) {
                        $retorno->put('idestado', $estado->id);
                        $retorno->put('idpais', $estado->pais_id);
                        unset($estado);
                    }
                }

                if($retorno->get('municipio')) {
                    $cidade = Cidade::whereNome($retorno->get('municipio'))->first();
                    if($cidade) {
                        $retorno->put('idcidade', $cidade->id);
                        unset($cidade);
                    }
                }

                $retorno->put('localidade', $retorno->get('municipio'));
                $retorno->put('razao_social', $retorno->get('nome'));
                $retorno->put('nome_fantasia', (!$retorno->get('fantasia')) ? $retorno->get('razao_social') : $retorno->get('fantasia'));

                return json_encode($retorno->only('nome_fantasia', 'razao_social', 'cnpj', 'cep', 'uf', 'logradouro', 'bairro', 'numero', 'localidade', 'complemento', 'idestado', 'idcidade', 'idpais', 'status'));
            });
        }else{
            $retorno = cache()->get('api.cnpj.'.$cnpj);
        }

        return $retorno;
    }
}
