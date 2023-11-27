<?php

namespace App\Livewire\Tributacoes\Cests;

use App\Models\Tributacoes\Cest;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\Actions;

class CestEditModal extends Component
{
    use Actions;

    public $cestEditModal = false;
 
    public Cest $cest;
 
    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:9|max:9', as:'cest')]
    public $cest_label;

    #[Validate('min:0', as:'ncm')]
    public $ncm_id;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->cest = Cest::find($rowId);

        $this->cest_label = $this->cest->cest;
        $this->fill($this->cest->only(['descricao', 'ncm_id']));

        $this->js('$openModal("cestEditModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $this->validate([
            "cest_label" => "unique:trib_cest,cest,{$this->cest->id}",
        ]);

        $validated['cest'] = $this->cest_label;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste cest?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->cest->update($validated);

            $this->reset('cestEditModal');
    
            $this->notification([
                'title'       => 'Cest atualizado!',
                'description' => 'Cest foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Cest.',
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
                'description' => 'Deletar este cest?',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => 'Deleted',
            ]);
            return;
        }

        try {
            $this->cest->delete();

            $this->reset('cestEditModal');

            $this->notification([
                'title'       => 'Cest deletado!',
                'description' => 'Cest foi deletado com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar o Cest.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.cests.cest-edit-modal');
    }
}
