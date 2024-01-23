<?php

namespace App\Livewire\Tributacoes\Cfops;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class CfopIndex extends Component
{
    public function render()
    {
        return view('livewire.tributacoes.cfops.cfop-index');
    }
}
