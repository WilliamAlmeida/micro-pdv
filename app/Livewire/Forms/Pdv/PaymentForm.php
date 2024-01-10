<?php

namespace App\Livewire\Forms\Pdv;

use Livewire\Attributes\Locked;
use Livewire\Form;

class PaymentForm extends Form
{
    public $valor_total = 0;

    #[Locked]
    public $cliente_selecionado;

    public $convenio = false;
    public $cliente_id;

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

    public function storePayment($caixa): string|null
    {
        try {
            if($this->dinheiro) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'dinheiro',
                    'valor' => $this->currency2Decimal($this->dinheiro)
                ]);
            }
    
            if($this->ticket) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'ticket',
                    'valor' => $this->currency2Decimal($this->ticket)
                ]);
            }
    
            if($this->cartao_debito) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'cartao_debito',
                    'valor' => $this->currency2Decimal($this->cartao_debito)
                ]);
            }
    
            if($this->cartao_credito) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
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

    public function resetAgreetment(): void
    {
        $this->reset('convenio_nome', 'convenio_matricula', 'convenio_selecionado');
    }

    public function storeAgreetment($caixa): string|null
    {
        try {
            $convenio = $caixa->venda->convenio()->create([
                'caixa_id' => $caixa->id,
                'clientes_id' => $this->cliente_id,
            ]);

            foreach($caixa->venda->itens as $item) {
                $convenio->itens()->create($item->toArray());
            }

            $caixa->venda->pagamentos()->create([
                'caixa_id' => $caixa->id,
                'forma_pagamento' => 'convenio',
                'valor' => 0
            ]);

            return null;

        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
    }
}
