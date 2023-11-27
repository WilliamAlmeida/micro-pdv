<?php

namespace App\Livewire\Tributacoes\Cests;

use App\Models\Tributacoes\Cest;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class CestCreateModal extends Component
{
    use Actions;

    public $cestCreateModal = false;
 
    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:9|max:9|unique:trib_cest,cest', as:'cest')]
    public $cest_label;

    #[Validate('min:0', as:'ncm')]
    public $ncm_id;

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("cestCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $validated['cest'] = $this->cest_label;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo cest?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $cest = Cest::create($validated);

            $this->reset('cestCreateModal');
    
            $this->notification([
                'title'       => 'Cest registrado!',
                'description' => 'Cest foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Cest.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.cests.cest-create-modal');
    }
}
