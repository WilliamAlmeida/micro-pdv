<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class UsuarioCreateModal extends Component
{
    use Actions;

    public $usuarioCreateModal = false;
 
    #[Validate('required|min:3|unique:users,name', as:'nome')]
    public $name;

    #[Validate('required|email|unique:users,email')]
    public $email;

    #[Validate('min:4')]
    public $password;

    #[Validate('min:4|same:password')]
    public $password_confirmation;

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("usuarioCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo usuário?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $user = User::create($validated);

            $this->reset('usuarioCreateModal');
    
            $this->notification([
                'title'       => 'Usuário registrado!',
                'description' => 'Usuário foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.usuarios.usuario-create-modal');
    }
}
