<?php

namespace App\Livewire\Forms\Pdv\Convenio;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DivideItemForm extends Form
{
    #[Validate('required|min:1|numeric')]
    public $quantidade = 0;
}
