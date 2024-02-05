<?php

namespace App\Livewire\Admin\Roles;

use App\Traits\HelperActions;
use Livewire\Attributes\Locked;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleCreateModal extends Component
{
    use Actions;
    use HelperActions;

    public $roleCreateModal = false;
 
    #[Validate('required|min:3|unique:roles,name', as:'função')]
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

    #[\Livewire\Attributes\On('create')]
    public function create(int $role_id=null): void
    {
        $this->resetValidation();

        $this->reset('name', 'tenant_id', 'selected');

        if($role_id) {
            $this->role = Role::with('permissions:id,name')->findOrFail($role_id);

            $this->name = $this->role->name;

            $permissions = $this->role->permissions->pluck('name')->all();
            unset($this->role->permissions);
            $this->role->permissions = $permissions;
            unset($permissions);
        }

        $this->set_focus('role_create');

        $this->js('$openModal("roleCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar esta nova função?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $role = Role::create($validated);

            $role->syncPermissions($this->selected);

            $this->reset('roleCreateModal');
    
            $this->notification([
                'title'       => 'Função registrada!',
                'description' => 'Função foi registrada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar a Função.',
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
        return view('livewire.admin.roles.role-create-modal');
    }
}
