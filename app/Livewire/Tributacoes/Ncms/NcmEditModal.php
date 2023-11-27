<?php

namespace App\Livewire\Tributacoes\Ncms;

use App\Models\Tributacoes\Ncm;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\Actions;

class NcmEditModal extends Component
{
    use Actions;

    public $ncmEditModal = false;
 
    public Ncm $ncm;

    #[Validate('required|min:3', as:'descrição')]
    public $descricao;

    #[Validate('required|min:8|max:10', as:'ncm')]
    public $ncm_label;

    #[Validate('required|min:0|max:99.99|numeric')]
    public $aliq_ipi;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->ncm = Ncm::find($rowId);

        $this->ncm_label = $this->ncm->ncm;
        $this->fill($this->ncm->only('descricao', 'aliq_ipi'));

        $this->js('$openModal("ncmEditModal")');
    }

    public function save($params=null)
    {
        $this->validate();

        $validated = $this->validate([
            "ncm_label" => "unique:trib_ncm_tipi,ncm,{$this->ncm->id}",
        ]);

        $validated['aliq_ipi'] = floatval($this->aliq_ipi);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste ncm?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->ncm->update($validated);

            $this->reset('ncmEditModal');
    
            $this->notification([
                'title'       => 'Ncm atualizado!',
                'description' => 'Ncm foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Ncm.',
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
                'description' => 'Deletar este ncm?',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => 'Deleted',
            ]);
            return;
        }

        try {
            $this->ncm->delete();

            $this->reset('ncmEditModal');

            $this->notification([
                'title'       => 'Ncm deletado!',
                'description' => 'Ncm foi deletado com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar o Ncm.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tributacoes.ncms.ncm-edit-modal');
    }
}
