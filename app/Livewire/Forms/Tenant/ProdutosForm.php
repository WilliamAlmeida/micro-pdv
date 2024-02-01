<?php

namespace App\Livewire\Forms\Tenant;

use Livewire\Form;
use App\Models\Tenant\Produtos;
use Livewire\Attributes\Validate;

class ProdutosForm extends Form
{
    #[Validate('min:0', as: 'tipo empresa')]
    public $empresas_id;

    #[Validate('required|min:0')]
    public $categoria;
    
    #[Validate('required|min:0|max:255')]
    public $titulo;
    
    #[Validate('min:0|max:255')]
    public $slug;
    
    #[Validate('min:0|max:255', as: 'descrição')]
    public $descricao;
    
    // #[Validate('min:0|max:255', as: 'código de barras')]
    // public $codigo_barras_1;
    
    // #[Validate('min:0|max:255', as: 'código de barras')]
    // public $codigo_barras_2;
    
    // #[Validate('min:0|max:255', as: 'código de barras')]
    // public $codigo_barras_3;
    
    #[Validate('required|min:0.1|numeric', as: 'preço')]
    public $preco_varejo;
    
    // #[Validate('nullable|min:0.1|numeric', as: 'preço')]
    // public $preco_atacado;
    
    // #[Validate('nullable|min:0.1|numeric', as: 'valor do garçom')]
    // public $valor_garcom;
    
    #[Validate('nullable|min:0.1|numeric', as: 'preço promocional')]
    public $preco_promocao;
    
    #[Validate('nullable|min:0|date', as: 'inicio da promoção')]
    public $promocao_inicio;
    
    #[Validate('nullable|min:0|date|after_or_equal:promocao_inicio', as: 'fim da promoção')]
    public $promocao_fim;
    
    // #[Validate('min:0', as: 'icms')]
    // public $trib_icms;
    
    // #[Validate('min:0', as: 'csosn')]
    // public $trib_csosn;
    
    // #[Validate('min:0', as: 'cst')]
    // public $trib_cst;
    
    // #[Validate('min:0', as: 'origem do produto')]
    // public $trib_origem_produto;
    
    // #[Validate('min:0', as: 'cfop dentro do estado')]
    // public $trib_cfop_de;
    
    // #[Validate('min:0', as: 'cfop fora do estado')]
    // public $trib_cfop_fe;
    
    // #[Validate('min:0', as: 'ncm')]
    // public $trib_ncm;
    
    // #[Validate('min:0', as: 'cest')]
    // public $trib_cest;
    
    #[Validate('min:0')]
    public $estoque_atual = 0;
    
    // #[Validate('min:0')]
    // public $unidade_medida;
    
    // #[Validate('min:0')]
    // public $codigo_externo;
    
    #[Validate('min:0|max:1')]
    public $destaque = 0;
    
    // #[Validate('min:0')]
    // public $views;
    
    // #[Validate('min:0|max:1')]
    // public $somente_mesa;
    
    // #[Validate('min:0')]
    // public $ordem;

    public function mount(Produtos $produto)
    {
        if($produto) {
            $this->fill($produto);

            $this->categoria = $produto->categorias->first()->id ?? null;
        }
    }
}
