<?php

namespace App\Livewire\Estoque;

use App\Livewire\Forms\Tenant\EstoqueForm;
use Livewire\Component;
use App\Models\Tenant\EstoqueMovimentacoes;
use App\Models\Tenant\Produtos;
use Illuminate\Support\Facades\DB;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class EstoqueCreateModal extends Component
{
    use Actions;

    public EstoqueForm $form;

    public $estoqueCreateModal = false;
    
    #[Locked]
    public $type;

    public $array_fornecedores = [];

    public Produtos $produto;

    #[\Livewire\Attributes\On('create')]
    public function create($type): void
    {
        $this->form->resetValidation();

        $this->reset('produto');
        $this->form->reset();

        $this->type = $type;

        $this->js('$openModal("estoqueCreateModal")');
    }

    public function fillProduto()
    {
        try {
            $this->produto = Produtos::withTrashed()->findOrFail($this->form->produtos_id);
        } catch (\Throwable $th) {
            // throw $th;

            $this->cleanProduto();
        }
    }

    public function cleanProduto()
    {
        $this->reset('produto');
        $this->form->reset();
    }

    public function save_entrada($params=null)
    {
        $validated = $this->form->validate();
        
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Dar entrada no estoque neste produto?',
                'acceptLabel' => 'Sim, continue',
                'method'      => 'save_entrada',
                'params'      => 'Saved',
            ]);
            return;
        }
        
        $validated['tipo'] = 'entrada';
        
        DB::beginTransaction();
        
        try {
            $estoque = EstoqueMovimentacoes::create($validated);
            
            $this->produto->update(['estoque_atual' => $this->produto->estoque_atual + $this->form->quantidade]);
            
            DB::commit();

            $this->reset('estoqueCreateModal');
    
            $this->notification([
                'title'       => 'Entrada registrada!',
                'description' => 'Entrada no Estoque foi registrada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
            
            DB::rollBack();
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel finalizar a Entrada no Estoque.',
                'icon'        => 'error'
            ]);
        }
    }

    public function save_baixa($params=null)
    {
        $validated = $this->form->validate();
        
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Dar baixa no estoque neste produto?',
                'acceptLabel' => 'Sim, continue',
                'method'      => 'save_baixa',
                'params'      => 'Saved',
            ]);
            return;
        }
        
        $validated['tipo'] = 'baixa';
        
        DB::beginTransaction();
        
        try {
            $estoque = EstoqueMovimentacoes::create($validated);
            
            $this->produto->update(['estoque_atual' => $this->produto->estoque_atual - $this->form->quantidade]);
            
            DB::commit();

            $this->reset('estoqueCreateModal');
    
            $this->notification([
                'title'       => 'Baixa registrada!',
                'description' => 'Baixa no Estoque foi registrada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
            
            DB::rollBack();
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel finalizar a Baixa no Estoque.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.estoque.estoque-create-modal', [
            'empresa' => auth()->user()->empresas_id
        ]);
    }
}
