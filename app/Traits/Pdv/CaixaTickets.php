<?php

namespace App\Traits\Pdv;

use App\Models\Empresas;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Impressoes;
use App\Models\Tenant\VendasHead;
use App\Models\Tenant\CaixaSangriaEntrada;

trait CaixaTickets
{
    public function printTicket(int $id, array $options = [])
    {
        $venda = VendasHead::with('impressoes', 'pagamentos')->whereId($id)->first();
        
        if(!$venda) {
            return ['error' => 0, 'message' => 'Venda não encontrada'];
        }

        try {
            if($venda->impressoes->count()) {
                $venda->impressoes()->delete();
            }
    
            $empresa = Empresas::with('logo')->first();
    
            $config_print = (object)[];
            $config_print->text_transform = 'uppercase';
    
            $html = view('components.tickets.venda_nao_fiscal', compact('empresa', 'venda', 'config_print'));

            $html = $this->minifyHtmlCss($html);
    
            $ticket = Impressoes::create([
                'rel_table' => 'vendas',
                'rel_id' => $venda->id,
                'tipo' => 1,
                'html' => $html
            ]);
    
            if(!$ticket) {
                return ['error' => -2, 'message' => 'Não foi possivel imprimir a Sangria'];
            }

            return ['message' => 'Ticket enviado para impressão', 'ticket' => $ticket->toArray()];
        } catch (\Throwable $th) {
            //throw $th;

            return ['error' => -1, 'message' => $th->getMessage()];
        }
    }

    public function printTicket_Last(int|Caixa $caixa, array $options = [])
    {
        if(is_int($caixa)) {
            $caixa = Caixa::with('ultima_venda')->find($caixa);
        }

        if(!$caixa) {
            return ['error' => 0, 'message' => 'Caixa não encontrado'];
        }

        if($caixa->status) {
            return ['error' => -1, 'message' => 'Caixa já fechado'];
        }

        $venda = $caixa->ultima_venda;

        if(!$venda) {
            return ['error' => -2, 'message' => 'Venda não encontrada'];
        }

        return $this->printTicket($venda->id, $options);
    }

    public function printSangria(int $id, array $options = [])
    {
        $dados = CaixaSangriaEntrada::with('impressoes')->whereTipo('s')->whereId($id)->first();

        if(!$dados) {
            return ['error' => 0, 'message' => 'Sangria não encontrada'];
        }

        try {
            if($dados->impressoes->count()) {
                $dados->impressoes()->delete();
            }
    
            $empresa = Empresas::with('logo')->first();
    
            $config_print = (object)[];
            $config_print->text_transform = 'uppercase';
    
            $html = view('components.tickets.sangria_entrada', compact('empresa', 'dados', 'config_print'));

            $html = $this->minifyHtmlCss($html);
    
            $ticket = Impressoes::create([
                'rel_table' => 'sangrias',
                'rel_id' => $dados->id,
                'tipo' => 1,
                'html' => $html
            ]);
    
            if(!$ticket) {
                return ['error' => -2, 'message' => 'Não foi possivel imprimir a Sangria'];
            }

            return ['message' => 'Sangria enviado para impressão', 'ticket' => $ticket->toArray()];
        } catch (\Throwable $th) {
            //throw $th;

            return ['error' => -1, 'message' => $th->getMessage()];
        }
    }

    public function printEntrada(int $id, array $options = [])
    {
        $dados = CaixaSangriaEntrada::with('impressoes')->whereTipo('e')->whereId($id)->first();

        if(!$dados) {
            return ['error' => 0, 'message' => 'Entrada não encontrada'];
        }

        try {
            if($dados->impressoes->count()) {
                $dados->impressoes()->delete();
            }
    
            $empresa = Empresas::with('logo')->first();
    
            $config_print = (object)[];
            $config_print->text_transform = 'uppercase';
    
            $html = view('components.tickets.sangria_entrada', compact('empresa', 'dados', 'config_print'));

            $html = $this->minifyHtmlCss($html);
    
            $ticket = Impressoes::create([
                'rel_table' => 'entradas',
                'rel_id' => $dados->id,
                'tipo' => 1,
                'html' => $html
            ]);
    
            if(!$ticket) {
                return ['error' => -2, 'message' => 'Não foi possivel imprimir a Sangria'];
            }

            return ['message' => 'Entrada enviada para impressão', 'ticket' => $ticket->toArray()];
        } catch (\Throwable $th) {
            //throw $th;

            return ['error' => -1, 'message' => $th->getMessage()];
        }
    }

    private function minifyHtmlCss($code) {
        // Remove espaços em branco desnecessários e quebras de linha do HTML e CSS
        $code = preg_replace('/\s+/', ' ', $code);
    
        // Remove espaços em branco antes e depois das tags
        $code = preg_replace('/\s*<\s*/', '<', $code);
        $code = preg_replace('/\s*>\s*/', '>', $code);
    
        // Remove espaços em branco entre as propriedades CSS
        $code = preg_replace('/\s*:\s*/', ':', $code);
        $code = preg_replace('/\s*;\s*/', ';', $code);
    
        return $code;
    }
}
