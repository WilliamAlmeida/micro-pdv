<?php

namespace App\Livewire\Tributacoes\Cfops;

use App\Models\Tributacoes\Cfop;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class CfopCreateModal extends Component
{
    use Actions;

    public $cfopCreateModal = false;
 
    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:5|max:5|unique:trib_cfop,cfop', as:'cfop')]
    public $cfop_label;

    #[Validate('min:3', as:'aplicação')]
    public $aplicacao;

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("cfopCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $validated['cfop'] = $this->cfop_label;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo cfop?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $cfop = Cfop::create($validated);

            $this->reset('cfopCreateModal');
    
            $this->notification([
                'title'       => 'Cfop registrado!',
                'description' => 'Cfop foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Cfop.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.cfops.cfop-create-modal');
    }
}
