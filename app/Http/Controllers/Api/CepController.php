<?php

namespace App\Http\Controllers\Api;

use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function show(string $cep)
    {
        $cep = preg_replace( '/[^0-9]/', '', $cep);

        if(strlen($cep) != 8) return json_encode(['status' => 'ERROR', 'message' => 'CEP invalido.', 'status_code' => 400]);

        if(!cache()->has('api.cep.'.$cep)) {
            if(cache()->has('api.cep.request_limited')) {
                return cache()->get('api.cep.request_limited');
            }

            $response = Http::get("http://viacep.com.br/ws/".$cep."/json");

            if(!$response->successful()) {
                return cache()->remember('api.cep.request_limited', 60, function () use ($response) {
                    if($response->status() == 429) {
                        return json_encode(['status' => 'ERROR', 'message' => 'Muitas requisições. Por favor tente após 1 minuto.', 'status_code' => $response->status()]);
                    }else{
                        return json_encode(['status' => 'ERROR', 'message' => 'Falha ao buscar o cep. Por favor tente após 1 minuto', 'status_code' => $response->status()]);
                    }
                });
            }

            $retorno = collect($response->json());

            if($retorno->has('erro')) {
                return cache()->remember('api.cep.'.$cep, 60 * 5, function () use ($retorno) {
                    return json_encode(['status' => 'ERROR', 'message' => 'CEP invalido.', 'status_code' => 400]);
                });
            }

            $retorno = cache()->remember('api.cep.'.$cep, 60 * 5, function () use ($retorno) {
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

                if($retorno->get('localidade')) {
                    $cidade = Cidade::whereNome($retorno->get('localidade'))->first();
                    if($cidade) {
                        $retorno->put('idcidade', $cidade->id);
                        unset($cidade);
                    }
                }

                $retorno->put('numero', $retorno->get('numero'));
                $retorno->put('razao_social', $retorno->get('nome'));
                $retorno->put('nome_fantasia', (!$retorno->get('fantasia')) ? $retorno->get('razao_social') : $retorno->get('fantasia'));
                $retorno->put('status', 'OK');

                return json_encode($retorno->only('cep', 'uf', 'logradouro', 'bairro', 'numero', 'localidade', 'complemento', 'idestado', 'idcidade', 'idpais', 'status'));
            });
        }else{
            $retorno = cache()->get('api.cep.'.$cep);
        }

        return $retorno;
    }
}
