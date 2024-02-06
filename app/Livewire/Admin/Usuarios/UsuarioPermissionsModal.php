<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use WireUi\Traits\Actions;

class UsuarioPermissionsModal extends Component
{
    use Actions;

    public $usuarioPermissionModal = false;

    #[Locked]
    public $roles = [];
 
    #[Locked]
    public User $user;

    #[Locked]
    public $user_roles = [];

    public $selecteds = [];

    public function mount()
    {
        $this->roles = Role::with(['permissions:id,name'])->orderBy('name')->get(['id', 'name']);
    }

    #[\Livewire\Attributes\On('permission')]
    public function edit($rowId): void
    {
        setPermissionsTeamId(0);
        $this->user = User::withTrashed()->with('roles.permissions')->find($rowId);

        $this->user_roles = $this->user->roles;

        $this->selecteds = $this->user->roles->pluck('id')->all();

        unset($this->user->roles);

        $this->js('$openModal("usuarioPermissionModal")');
    }

    public function save($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as permissões deste usuário?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            setPermissionsTeamId(0);
            $this->user->syncRoles($this->selecteds);

            $this->reset('usuarioPermissionModal');
    
            $this->notification([
                'title'       => 'Permissões atualizadas!',
                'description' => 'Permissões foram atualizadas com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar as Permissões.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.usuarios.usuario-permissions-modal');
    }
}
