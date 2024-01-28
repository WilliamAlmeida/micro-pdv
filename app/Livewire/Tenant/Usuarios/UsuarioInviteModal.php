<?php

namespace App\Livewire\Tenant\Usuarios;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendTenantInviteUserJob;
use App\Mail\TenantInviteUserMail;

class UsuarioInviteModal extends Component
{
    use Actions;

    public $usuarioInviteModal = false;

    #[Validate('required|email')]
    public $email;

    #[\Livewire\Attributes\On('invite')]
    public function invite(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("usuarioInviteModal")');
    }

    public function send($params=null)
    {
        $validated = $this->validate();

        $user = User::withoutTenancy()->with('tenants:id')->whereEmail($this->email)->first();

        if(!$user) {
            return $this->addError('email', 'Nenhum usuário com este e-mail foi encontrado, portante você precisa cadastra-lo.');
        }

        if($user->tenants->firstWhere('id', tenant('id'))) {
            return $this->addError('email', 'Usuário já esta vinculado a sua empresa.');
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Convidar este usuário?',
                'acceptLabel' => 'Sim, convide-o',
                'method'      => 'send',
                'params'      => 'Confirm',
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            SendTenantInviteUserJob::dispatch($user)->onQueue('default');

            $this->reset('usuarioInviteModal');
    
            $this->notification([
                'title'       => 'Convite gerado com sucesso!',
                'description' => 'O convite será enviado para E-mail do Usuário.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no convite!',
                'description' => 'Não foi possivel gerar o convite do Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.usuarios.usuario-invite-modal');
    }
}
