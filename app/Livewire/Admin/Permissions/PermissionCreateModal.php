<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use App\Traits\HelperActions;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionCreateModal extends Component
{
    use Actions;
    use HelperActions;

    public $permissionCreateModal = false;
 
    #[Validate('required|min:3', as:'permissão')]
    public $name;

    #[Locked]
    public Permission $permission;

    public $selected = [];

    public $roles = [];

    public function mount()
    {
        $this->roles = $this->getRoles();
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
    public function create(): void
    {
        $this->resetValidation();

        $this->reset('name', 'selected');

        $this->set_focus('permission_create');

        $this->js('$openModal("permissionCreateModal")');
    }

    public function save($params=null)
    {
        $this->validate();

        $permissions = explode(';', $this->name);
        $permissions = collect($permissions)->map(function($item) {
            return ['guard_name' => 'web', 'name' => Str::squish($item)];
        });

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar esta nova permissão?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            Permission::insert($permissions->all());

            $permissions = Permission::whereIn('name', $permissions->pluck('name'))->get();

            foreach ($permissions as $permission) {
                $permission->assignRole($this->selected);
            }

            $this->reset('permissionCreateModal');
    
            $this->notification([
                'title'       => 'Permissão registrada!',
                'description' => 'Permissão foi registrada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();

            // throw $th;

            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar a Permissão.',
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
        return view('livewire.admin.permissions.permission-create-modal');
    }
}
