<?php

namespace App\Livewire\Forms;

use App\Models\Empresas;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmpresaForm extends Form
{
    #[Validate('min:0', as: 'tipo empresa')]
    public $id_tipo_empresa;

    #[Validate('required|min:0|max:255')]
    public $nome_fantasia;

    #[Validate('required|min:0|max:255')]
    public $slug;

    #[Validate('required|min:0|max:255')]
    public $razao_social;

    #[Validate('min:0')]
    public $idpais;

    #[Validate('required', as: 'estado')]
    public $idestado;

    #[Validate('required', as: 'cidade')]
    public $idcidade;

    #[Validate('nullable|min:16|max:18')]
    public $cnpj;

    #[Validate('nullable|min:10|max:20')]
    public $inscricao_estadual;

    #[Validate('nullable|min:14|max:14')]
    public $cpf;

    #[Validate('required|min:0|max:255', as: 'logradouro')]
    public $end_logradouro;

    #[Validate('required|min:0|max:10', as: 'número')]
    public $end_numero;

    #[Validate('min:0|max:255', as: 'complemento')]
    public $end_complemento;

    #[Validate('required|min:0|max:255', as: 'bairro')]
    public $end_bairro;

    #[Validate('required|min:0|max:255', as: 'município')]
    public $end_cidade;

    #[Validate('required|min:0|max:14', as: 'cep')]
    public $end_cep;

    // #[Validate('min:0')]
    // public $file_ticket;

    // #[Validate('min:0')]
    // public $file_logo;

    // #[Validate('min:0')]
    // public $file_background;

    // #[Validate('min:0')]
    // public $whatsapp;

    // #[Validate('min:0')]
    // public $whatsapp_status;

    // #[Validate('min:0')]
    // public $tema;

    #[Validate('min:0')]
    public $keywords;

    #[Validate('min:0|max:160')]
    public $description;

    // #[Validate('min:0|max:1|numeric')]
    // public $status;

    // #[Validate('min:0|max:1|numeric')]
    // public $status_manual;

    // #[Validate('min:0|max:1|numeric')]
    // public $status_mesa;

    // #[Validate('min:0|max:1|numeric')]
    // public $impressao;

    // #[Validate('min:0|max:1|numeric')]
    // public $impressao_mesa;

    // #[Validate('min:0')]
    // public $taxa_entrega;

    // #[Validate('min:0')]
    // public $valor_min_entrega;

    // #[Validate('min:0')]
    // public $isento_taxa_entrega;

    // #[Validate('min:0')]
    // public $negar_entrega;

    // #[Validate('min:0')]
    // public $tempo_entrega_min;

    // #[Validate('min:0')]
    // public $tempo_entrega_max;

    // #[Validate('min:0')]
    // public $ultimo_pedido;

    // #[Validate('min:0')]
    // public $couvert;

    // #[Validate('min:0')]
    // public $garcom;

    // #[Validate('min:0')]
    // public $rate;

    // #[Validate('min:0|max:15')]
    // public $manifest_v;

    // #[Validate('min:0')]
    // public $s_mesa;

    public $horarios = [];
    protected $horarios_model = ['inicio' => null, 'fim' => null];

    public function mount(Empresas $empresa)
    {
        if($empresa) {
            $this->fill($empresa);
            
            $this->horarios = [
                'Mon' => [],
                'Tue' => [],
                'Wed' => [],
                'Thu' => [],
                'Fri' => [],
                'Sat' => [],
                'Sun' => [],
            ];

            foreach($empresa->horarios as $key => $values) {
                $this->horarios[$values->dia][] = ['inicio' => $values->inicio, 'fim' => $values->fim];
            }
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

    public function add_hours($day_of_week)
    {
        $this->horarios[$day_of_week][] = $this->horarios_model;
        // $this->horarios[$day_of_week] = array_values($this->horarios[$day_of_week]);
    }

    public function remove_hours($day_of_week, $index)
    {
        unset($this->horarios[$day_of_week][$index]);
        // $this->horarios[$day_of_week] = array_values($this->horarios[$day_of_week]);
    }
}
