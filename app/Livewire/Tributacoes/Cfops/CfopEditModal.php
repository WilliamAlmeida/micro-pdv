<?php

namespace App\Livewire\Tributacoes\Cfops;

use App\Models\Tributacoes\Cfop;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\Actions;

class CfopEditModal extends Component
{
    use Actions;

    public $cfopEditModal = false;
 
    public Cfop $cfop;

    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:5|max:5', as:'cfop')]
    public $cfop_label;

    #[Validate('min:3', as:'aplicação')]
    public $aplicacao;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->cfop = Cfop::find($rowId);

        $this->cfop_label = $this->cfop->cfop;
        $this->fill($this->cfop->only(['descricao', 'aplicacao']));

        $this->js('$openModal("cfopEditModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $this->validate([
            "cfop_label" => "unique:trib_cfop,cfop,{$this->cfop->id}",
        ]);

        $validated['cfop'] = $this->cfop_label;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste cfop?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->cfop->update($validated);

            $this->reset('cfopEditModal');
    
            $this->notification([
                'title'       => 'Cfop atualizado!',
                'description' => 'Cfop foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Cfop.',
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
                'description' => 'Deletar este cfop?',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => 'Deleted',
            ]);
            return;
        }

        try {
            $this->cfop->delete();

            $this->reset('cfopEditModal');

            $this->notification([
                'title'       => 'Cfop deletado!',
                'description' => 'Cfop foi deletado com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar o Cfop.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.cfops.cfop-edit-modal');
    }
}
