<?php

namespace App\Livewire\Forms\Pdv\Caixa;

use Livewire\Form;
use Livewire\Attributes\Validate;

class EntradaForm extends Form
{
    #[Validate('required|min:0.1|numeric')]
    public $valor;

    #[Validate('nullable|min:3|max:255')]
    public $motivo;
}
