<?php

namespace App\Livewire\Conta;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ContaEdit extends Component
{
    use Actions;

    public $name;
    public $email;

    public $passwordForm = false;
    public $current_password;
    public $password;
    public $password_confirmation;

    #[Validate('required|current_password', as: 'password')]
    public $password_to_delete;

    public $deleteAccountModal = false;

    public function mount()
    {
        $this->fill(auth()->user()->only('name', 'email'));
    }

    public function cancel_validation()
    {
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->resetValidation();
    }

    public function atualizar_conta()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore(auth()->id())],
        ]);

        $this->email = Str::lower($this->email);

        $usuario = auth()->user()->fill($this->only('name', 'email'));

        if($usuario->isDirty('email')) {
            $usuario->email_verified_at = null;
        }

        try {
            $usuario->save();
    
            $this->notification([
                'title'       => 'Perfil atualizado!',
                'description' => 'Perfil foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->mount();

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Perfil.',
                'icon'        => 'error'
            ]);
        }
    }

    public function atualizar_senha()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()->min(4), 'confirmed'],
            'password_confirmation' => ['required', Password::defaults()->min(4)]
        ]);

        try {
            auth()->user()->update([
                'password' => Hash::make($this->password),
            ]);
    
            $this->notification([
                'title'       => 'Senha atualizada!',
                'description' => 'Senha foi atualizada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->reset('current_password', 'password', 'password_confirmation', 'passwordForm');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar a Senha.',
                'icon'        => 'error'
            ]);
        }
    }

    public function deletar_conta()
    {
        $this->validateOnly('password_to_delete');

        $usuario = auth()->user();

        auth()->logout();

        // $usuario->delete();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(route('home'));
    }

    public function render()
    {
        $layout = tenant() ? 'components.layouts.tenant' : 'components.layouts.admin';

        return view('livewire.conta.conta-edit')->layout($layout);
    }
}
