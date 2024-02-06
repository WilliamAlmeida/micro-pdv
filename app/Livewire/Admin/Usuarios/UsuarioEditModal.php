<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\Actions;

class UsuarioEditModal extends Component
{
    use Actions;

    public $usuarioEditModal = false;
 
    public User $user;

    #[Validate('required|min:3', as:'nome')]
    public $name;

    #[Validate('required|email')]
    public $email;

    #[Validate('nullable|min:4')]
    public $password;

    #[Validate('nullable|min:4|same:password')]
    public $password_confirmation;

    #[Validate('min:0|max:2|numeric')]
    public $type;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->user = User::withTrashed()->find($rowId);

        $this->fill($this->user);

        $this->js('$openModal("usuarioEditModal")');
    }

    public function save($params=null)
    {
        $this->validate();

        $this->validate([
            "name" => "unique:users,name,{$this->user->id}",
            "email" => "unique:users,email,{$this->user->id}",
        ]);

        if(empty($this->password)) {
            $validated = $this->only('name', 'email', 'type');
        }else{
            $validated = $this->only('name', 'email', 'type', 'password');
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste usuário?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->user->update($validated);

            $this->reset('usuarioEditModal');
    
            $this->notification([
                'title'       => 'Usuário atualizado!',
                'description' => 'Usuário foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Deletar este usuário?',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => 'Deleted',
            ]);
            return;
        }

        try {
            $this->user->delete();

            $this->reset('usuarioEditModal');

            $this->notification([
                'title'       => 'Usuário deletado!',
                'description' => 'Usuário foi deletado com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar o Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function restore($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar este usuário?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->user->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Usuário já esta ativado.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->user->restore();

                $this->notification([
                    'title'       => 'Usuário restaurado!',
                    'description' => 'Usuário foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('usuarioEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.usuarios.usuario-edit-modal');
    }
}
