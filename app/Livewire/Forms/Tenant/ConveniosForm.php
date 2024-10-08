<?php

namespace App\Livewire\Forms\Tenant;

use Livewire\Form;
use Illuminate\Support\Str;
use App\Models\Tenant\Convenios;
use Livewire\Attributes\Validate;

class ConveniosForm extends Form
{
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

    public function mount(Convenios $convenio)
    {
        if($convenio) {
            $this->fill($convenio);
        }
    }

    public function fillCep($values)
    {
        $this->end_bairro       = Str::title($values->bairro ?: null);
        $this->end_cidade       = Str::title($values->localidade ?: null);
        $this->end_complemento  = Str::title($values->complemento ?: null);
        $this->end_logradouro   = Str::title($values->logradouro ?: null);
        $this->end_numero       = $values->numero ?: null;
        $this->idcidade         = $values->idcidade ?: null;
        $this->idestado         = $values->idestado ?: null;
        $this->idpais           = $values->idpais ?: null;
    }

    public function fillCnpj($values)
    {
        $this->nome_fantasia = $values->nome_fantasia;
        $this->razao_social = $values->razao_social;
        $this->slug = Str::slug($values->nome_fantasia);

        $this->end_cep = $values->cep ?: null;
        $this->fillCep($values);
    }
    
    public function validateCpfCnpj()
    {
        if(empty($this->cnpj) && empty($this->cpf)) {
            $this->addError('cnpj', 'Preencha o CPF ou CNPJ.');
            $this->addError('cpf', 'Preencha o CPF ou CNPJ.');
            return true;
        }
        
        return false;
    }
}
