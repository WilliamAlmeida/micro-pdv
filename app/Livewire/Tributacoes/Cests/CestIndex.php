<?php

namespace App\Livewire\Tributacoes\Cests;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class CestIndex extends Component
{
    public function render()
    {
        return view('livewire.tributacoes.cests.cest-index');
    }
}
