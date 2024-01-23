<?php

namespace App\Livewire\Produtos;

use App\Livewire\Forms\Tenant\ProdutosForm;
use App\Models\Tenant\Categorias;
use Livewire\Component;
use App\Models\Tenant\Produtos;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;

class ProdutoCreateModal extends Component
{
    use Actions;

    public ProdutosForm $form;

    public $produtoCreateModal = false;

    public $array_categorias = [];

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->form->reset();

        $this->array_categorias = Categorias::select('id', 'titulo')->get()->toArray();

        $this->js('$openModal("produtoCreateModal")');
    }

    public function save($params=null)
    {
        $this->form->validate([
            "titulo" => "required|unique:produtos,titulo",
        ]);

        $validated = $this->form->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo produto?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        $validated['slug'] = Str::slug($this->form->titulo);
        $validated['empresas_id'] = auth()->user()->empresas_id;

        try {
            $produto = Produtos::create($validated);

            $produto->categorias()->attach(['categorias_id' => $this->form->categoria]);

            $this->reset('produtoCreateModal');
    
            $this->notification([
                'title'       => 'Produto registrado!',
                'description' => 'Produto foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Produto.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.produtos.produto-create-modal');
    }
}
