<?php

namespace App\Livewire\Admin\Usuarios;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class UsuarioIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.usuarios.usuario-index');
    }
}
