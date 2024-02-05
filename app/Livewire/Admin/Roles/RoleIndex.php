<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class RoleIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.roles.role-index');
    }
}
