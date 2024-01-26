<?php

namespace App\Livewire\Tenant\Categorias;

use Livewire\Attributes\Locked;
use Livewire\Component;

class CategoriaIndex extends Component
{
    #[Locked]
    public $tenant;

    public function mount()
    {
        $this->tenant = tenant();
    }

    public function render()
    {
        return view('livewire.tenant.categorias.categoria-index');
    }
}
