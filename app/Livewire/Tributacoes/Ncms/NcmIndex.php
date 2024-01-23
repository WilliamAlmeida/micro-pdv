<?php

namespace App\Livewire\Tributacoes\Ncms;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class NcmIndex extends Component
{
    public function render()
    {
        return view('livewire.tributacoes.ncms.ncm-index');
    }
}
