<?php

namespace App\Traits\Pdv;

trait CaixaActions
{
    public function caixa_show()
    {
        if(!auth()->user()->caixa()->exists()) {
            $caixa = auth()->user()->caixa()->create([
                'tipo_venda' => 'caixa',
                'status' => 0,
            ]);
        }else{
            $caixa = auth()->user()->caixa()
            ->with('vendas','convenios_recebimentos')
            ->with(['venda' => fn($q) => $q->with('itens','pagamentos')])
            ->first();
            if($caixa && $caixa->venda) {
                $caixa->venda->update(['valor_total' => $caixa->venda->itens()->sum('valor_total')]);
            }
        }

        return $caixa;
    }
}
