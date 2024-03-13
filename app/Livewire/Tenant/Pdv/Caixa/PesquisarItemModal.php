<?php

namespace App\Livewire\Tenant\Pdv\Caixa;

use Livewire\Component;
use App\Models\Tenant\Produtos;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;

class PesquisarItemModal extends Component
{
    use Actions;

    public $searchProductModal = false;
    public $produtos_encontrados = [];

    #[On('pesquisar_produto')]
    public function pesquisar_produto($value)
    {
        if(empty($value)) return;

        $pesquisa = $value;

        $unique_key = sha1(auth()->id().'.pdv.pesquisar_produto'.$pesquisa);

        $produtos = cache()->remember($unique_key, 10, function() use ($pesquisa) {
            $produtos = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual');

            if(is_numeric($pesquisa)) {
                $produtos->where(function($query) use ($pesquisa) {
                    return $query->whereAny(['codigo_barras_1', 'codigo_barras_2', 'codigo_barras_3', 'id'], '=', $pesquisa);
                });
            }else{
                $produtos->where('titulo', 'like', '%'.$pesquisa.'%');
            }

            return $produtos->get()->toArray();
        });

        // $produtos = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual');

        // if(is_numeric($pesquisa)) {
        //     $produtos->where(function($query) use ($pesquisa) {
        //         return $query
        //             ->where('codigo_barras_1', $pesquisa)
        //             ->orWhere('codigo_barras_2', $pesquisa)
        //             ->orWhere('codigo_barras_3', $pesquisa)
        //             ->orWhere('id', $pesquisa);
        //     });
        // }else{
        //     $produtos->where('titulo', 'like', '%'.$pesquisa.'%');
        // }

        // $produtos = $produtos->get();

        $produtos_count = count($produtos);

        if(!$produtos_count) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Nenhum Produto encontrado.',
                'icon'        => 'warning'
            ]);
            return;
        }

        $this->produtos_encontrados = $produtos;
        
        if($produtos_count == 1) {
            $this->dispatch('selecionar_produto', [$produtos[0]['id']]);
        }else{
            $this->js('$openModal("searchProductModal")');
        }
    }

    public function render()
    {
        return view('livewire.tenant.pdv.caixa.pesquisar-item-modal');
    }
}
