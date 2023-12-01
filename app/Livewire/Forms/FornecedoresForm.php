<?php

namespace App\Livewire\Forms;

use App\Models\Fornecedores;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FornecedoresForm extends Form
{
    #[Validate('required|min:0', as: 'tipo fornecedor')]
    public $id_tipo_fornecedor;
    
    #[Validate('required|min:0|max:255')]
    public $nome_fantasia;
    
    #[Validate('min:0|max:255')]
    public $slug;
    
    #[Validate('required|min:0|max:255')]
    public $razao_social;
    
    #[Validate('min:0|max:255')]
    public $idpais;
    
    #[Validate('required|max:255')]
    public $idestado;
    
    #[Validate('max:255')]
    public $idcidade;
    
    #[Validate('min:0|max:18')]
    public $cnpj;
    
    #[Validate('min:0|max:20')]
    public $inscricao_estadual;
    
    #[Validate('min:0|max:14')]
    public $cpf;
    
    #[Validate('required|min:0|max:255', as: 'logradouro')]
    public $end_logradouro;
    
    #[Validate('required|min:0|max:10', as: 'numero')]
    public $end_numero;
    
    #[Validate('min:0|max:255', as: 'complemento')]
    public $end_complemento;
    
    #[Validate('required|min:0|max:255', as: 'bairro')]
    public $end_bairro;
    
    #[Validate('required|min:0|max:255', as: 'cidade')]
    public $end_cidade;
    
    #[Validate('min:0|max:255', as: 'estado')]
    public $end_uf;
    
    #[Validate('min:0|max:14', as: 'cep')]
    public $end_cep;
    
    #[Validate('min:0')]
    public $whatsapp;

    public function mount(Fornecedores $fornecedor)
    {
        if($fornecedor) {
            $this->fill($fornecedor);
        }
    }

    public function fillCep($values)
    {
        $this->end_bairro       = ucwords($values->bairro ?: null);
        $this->end_cidade       = ucwords($values->localidade ?: null);
        $this->end_complemento  = ucwords($values->complemento ?: null);
        $this->end_logradouro   = ucwords($values->logradouro ?: null);
        // $this->end_numero    = $values->numero ?: null;
        // $this->end_idcidade  = $values->idcidade ?: null;
        $this->idestado         = $values->idestado ?: null;
    }
}
