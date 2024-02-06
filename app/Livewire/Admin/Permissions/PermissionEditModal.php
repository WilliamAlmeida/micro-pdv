<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionEditModal extends Component
{
    use Actions;

    public $permissionEditModal = false;
 
    public Permission $permission;
 
    #[Validate('required|min:3', as:'permissão')]
    public $name;

    public $selected = [];

    #[Locked]
    public $roles = [];
    
    public function mount()
    {
        $this->roles = Role::orderBy('name')->get(['id', 'name']);
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->permission = Permission::with('roles')->find($rowId);

        $this->fill($this->permission->only('name'));

        $this->selected = $this->permission->roles->pluck('id')->all();

        $this->js('$openModal("permissionEditModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate([
            "name" => "unique:permissions,name,{$this->permission->id}",
        ]);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações desta permissão?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->permission->update($validated);

            $this->permission->syncRoles($this->selected);

            $this->reset('permissionEditModal');
    
            $this->notification([
                'title'       => 'Permissão atualizada!',
                'description' => 'Permissão foi atualizada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar a Permissão.',
                'icon'        => 'error'
            ]);
        }
    }

    private function getRoles()
    {
        cache()->forget('list.roles');

        return cache()->remember('list.roles', 60 * 2, function() {
            $roles = Role::orderBy('name')->get(['id', 'name']);

            return $roles->toArray();
        });
    }

    public function render()
    {
        return view('livewire.admin.permissions.permission-edit-modal');
    }
}
