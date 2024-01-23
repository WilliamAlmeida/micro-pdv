<?php

namespace App\Livewire\Forms\Tenant;

use Livewire\Form;
use Livewire\Attributes\Validate;

class EstoqueForm extends Form
{
    #[Validate('required|min:1', as: 'produto')]
    public $produtos_id;

    #[Validate('min:1|numeric')]
    public $quantidade = 0;

    #[Validate('nullable|min:5')]
    public $motivo;

    #[Validate('nullable|min:1')]
    public $fornecedores_id;

    #[Validate('nullable|min:44')]
    public $nota_fiscal;
}
