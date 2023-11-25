<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\Actions;

class UserEditModal extends Component
{
    use Actions;

    public $userEditModal = false;
 
    public User $user;

    #[Validate('required|min:3', as:'nome')]
    public $name;

    #[Validate('required|email')]
    public $email;

    #[Validate('nullable|min:4')]
    public $password;

    #[Validate('nullable|min:4|same:password')]
    public $password_confirmation;

    #[Validate('min:0|max:1|numeric')]
    public $tipo;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->user = User::find($rowId);

        $this->fill($this->user);

        $this->tipo = $this->user->is_admin;

        $this->js('$openModal("userEditModal")');
    }

    public function save($params=null)
    {
        $this->validate();

        $validated = $this->validate([
            "name" => "unique:users,name,{$this->user->id}",
            "email" => "unique:users,email,{$this->user->id}",
        ]);

        $validated['is_admin'] = $this->tipo;

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

            $this->reset('userEditModal');
    
            $this->notification([
                'title'       => 'Usuário atualizado!',
                'description' => 'Usuário foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
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

            $this->reset('userEditModal');

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

    public function render()
    {
        return view('livewire.usuarios.user-edit-modal');
    }
}
