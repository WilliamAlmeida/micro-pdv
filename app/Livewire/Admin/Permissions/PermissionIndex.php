<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class PermissionIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.permissions.permission-index');
    }
}
