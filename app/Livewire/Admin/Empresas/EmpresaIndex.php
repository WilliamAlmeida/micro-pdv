<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class EmpresaIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.empresas.empresa-index');
    }
}
