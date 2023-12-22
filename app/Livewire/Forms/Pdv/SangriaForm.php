<?php

namespace App\Livewire\Forms\Pdv;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SangriaForm extends Form
{
    #[Validate('required|min:0.1|numeric')]
    public $valor;

    #[Validate('nullable|min:3|max:255')]
    public $motivo;
}
