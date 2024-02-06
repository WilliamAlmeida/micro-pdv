<?php

namespace App\Livewire\Admin\Roles;

use App\Traits\HelperActions;
use Livewire\Attributes\Locked;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleEditModal extends Component
{
    use Actions;
    use HelperActions;

    public $roleEditModal = false;
 
    #[Validate('required|min:3', as:'função')]
    public $name;

    #[Validate('nullable|min:1')]
    public $tenant_id;

    #[Locked]
    public Role $role;

    public $selected = [];

    public $permissions = [];

    public function mount()
    {
        $this->permissions = $this->getPermissions();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <!-- Loading spinner... -->
        </div>
        HTML;
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->reset('name', 'tenant_id', 'selected');

        $this->role = Role::with('permissions:id,name')->findOrFail($rowId);

        $this->fill($this->role->only('name', 'tenant_id'));

        $this->selected = $this->role->permissions->pluck('id')->all();

        unset($this->role->permissions);

        $this->set_focus('role_edit');

        $this->js('$openModal("roleEditModal")');
    }
    
    public function save($params=null)
    {
        $validated = $this->validate([
            "name" => "unique:roles,name,{$this->role->id}",
        ]);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações desta função?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->role->update($validated);

            $this->role->syncPermissions($this->selected);

            $this->reset('roleEditModal');
    
            $this->notification([
                'title'       => 'Função atualizada!',
                'description' => 'Função foi atualizada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar a Função.',
                'icon'        => 'error'
            ]);
        }
    }

    private function getPermissions()
    {
        return cache()->remember('list.permissions', 60 * 2, function() {
            $permissions = Permission::orderBy('name')->get(['id', 'name']);

            $permissions = $permissions->mapToGroups(function($item, $key) {
                return [substr($item['name'], 0, strpos($item['name'], '.')) => $item->toArray()];
            });

            $permissions_tenant = $permissions['tenant']->mapToGroups(function($item) {
                return [substr($item['name'], 7, strpos(substr($item['name'], 7), '.')) => $item];
            });
            // $permissions_tenant = [];

            $permissions['tenant'] = $permissions_tenant;
            unset($permissions_tenant);

            return $permissions->toArray();
        });
    }

    public function render()
    {
        return view('livewire.admin.roles.role-edit-modal');
    }
}
