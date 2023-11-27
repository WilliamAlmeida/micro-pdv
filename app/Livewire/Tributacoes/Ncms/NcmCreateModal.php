<?php

namespace App\Livewire\Tributacoes\Ncms;

use App\Models\Tributacoes\Ncm;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class NcmCreateModal extends Component
{
    use Actions;

    public $ncmCreateModal = false;
 
    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:8|max:10|unique:trib_ncm_tipi,ncm', as:'ncm')]
    public $ncm_label;

    #[Validate('required|min:0|max:99.99|numeric')]
    public $aliq_ipi;

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("ncmCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $validated['aliq_ipi'] = floatval($validated['aliq_ipi']);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo ncm?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $user = Ncm::create($validated);

            $this->reset('ncmCreateModal');
    
            $this->notification([
                'title'       => 'Ncm registrado!',
                'description' => 'Ncm foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Ncm.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.ncms.ncm-create-modal');
    }
}
