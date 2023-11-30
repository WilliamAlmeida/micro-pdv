<?php

namespace App\Livewire\Categorias;

use Livewire\Component;
use App\Models\Categorias;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;

class CategoriaCreateModal extends Component
{
    use Actions;

    public $categoriaCreateModal = false;
 
    #[Validate('required|min:3|unique:categorias,titulo', as:'título')]
    public $titulo;

    #[Validate('min:0')]
    public $slug;

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("categoriaCreateModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar esta nova categoria?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        $validated['slug'] = Str::slug($this->titulo);
        $validated['empresas_id'] = auth()->user()->empresas_id;

        try {
            $categoria = Categorias::create($validated);

            $this->reset('categoriaCreateModal');
    
            $this->notification([
                'title'       => 'Categoria registrada!',
                'description' => 'Categoria foi registrada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar a Categoria.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.categorias.categoria-create-modal');
    }
}
