<?php

namespace App\Livewire\Forms\Pdv\Convenio;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ReceivementForm extends Form
{
    public $valor_total = 0;

    public $desconto;

    // #[Locked]
    public $informado = 0;
    // #[Locked]
    public $troco;

    public $dinheiro;
    public $ticket;
    public $cartao_debito;
    public $cartao_credito;

    public function currency2Decimal($value): float
    {
        $value = str_replace('.', ',', $value);
        return floatval(str_replace(',', '.', $value));
    }

    // public function calculeChangeBack($venda): void
    // {
    //     $this->informado = floatval($this->dinheiro) + floatval($this->ticket) + floatval($this->cartao_debito) + floatval($this->cartao_credito) + floatval($this->desconto);
    //     if($this->informado != 0) {
    //         $this->troco = floatval($this->informado) - floatval($venda->valor_total);
    //     }else{
    //         $this->reset('troco');
    //     }
    // }

    public function storePayment($recebimento): string|null
    {
        try {
            if($this->dinheiro) {
                $recebimento->pagamentos()->create([
                    // 'convenios_recebimentos_id' => $recebimento->id,
                    'forma_pagamento' => 'dinheiro',
                    'valor' => $this->currency2Decimal($this->dinheiro)
                ]);
            }
    
            if($this->ticket) {
                $recebimento->pagamentos()->create([
                    // 'convenios_recebimentos_id' => $recebimento->id,
                    'forma_pagamento' => 'ticket',
                    'valor' => $this->currency2Decimal($this->ticket)
                ]);
            }
    
            if($this->cartao_debito) {
                $recebimento->pagamentos()->create([
                    // 'convenios_recebimentos_id' => $recebimento->id,
                    'forma_pagamento' => 'cartao_debito',
                    'valor' => $this->currency2Decimal($this->cartao_debito)
                ]);
            }
    
            if($this->cartao_credito) {
                $recebimento->pagamentos()->create([
                    // 'convenios_recebimentos_id' => $recebimento->id,
                    'forma_pagamento' => 'cartao_credito',
                    'valor' => $this->currency2Decimal($this->cartao_credito)
                ]);
            }

            return null;

        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
    }
}
