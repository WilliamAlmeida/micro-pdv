<?php

namespace App\Livewire\Categorias;

use Livewire\Component;
use App\Models\Categorias;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;

class CategoriaEditModal extends Component
{
    use Actions;

    public $categoriaEditModal = false;
 
    public Categorias $categoria;

    #[Validate('required|min:3', as:'título')]
    public $titulo;

    #[Validate('min:0')]
    public $slug;

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->categoria = Categorias::withTrashed()->find($rowId);

        $this->fill($this->categoria);

        $this->js('$openModal("categoriaEditModal")');
    }

    public function save($params=null)
    {
        $validated = $this->validate();

        $this->validate([
            "titulo" => "unique:categorias,titulo,{$this->categoria->id}",
        ]);

        $validated['slug'] = Str::slug($this->titulo);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações desta categoria?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->categoria->update($validated);

            $this->reset('categoriaEditModal');
    
            $this->notification([
                'title'       => 'Categoria atualizada!',
                'description' => 'Categoria foi atualizada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar a Categoria.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            if($this->categoria->trashed()) {
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Deletar esta categoria?',
                    'acceptLabel' => 'Sim, delete',
                    'method'      => 'delete',
                    'params'      => 'Deleted',
                ]);
            }else{
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Desativar esta categoria?',
                    'acceptLabel' => 'Sim, desative',
                    'method'      => 'delete',
                    'params'      => 'Deactivate',
                ]);
            }
            return;
        }

        try {
            if($this->categoria->trashed()) {
                $produtos_count = $this->categoria->produtos()->withTrashed()->count();
                if($produtos_count) {
                    $this->notification([
                        'title'       => 'Falha ao deletar!',
                        'description' => "Esta Categoria está vinculada a {$produtos_count} produtos.",
                        'icon'        => 'error'
                    ]);
                    return;
                }else{
                    // $this->categoria->forceDelete();

                    $this->notification([
                        'title'       => 'Categoria deletada!',
                        'description' => 'Categoria foi deletada com sucesso',
                        'icon'        => 'success'
                    ]);
                }
            }else{
                $this->categoria->delete();

                $this->notification([
                    'title'       => 'Categoria desativada!',
                    'description' => 'Categoria foi desativada com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('categoriaEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            if($this->categoria->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao deletar!',
                    'description' => 'Não foi possivel deletar a Categoria.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Falha ao desativar!',
                    'description' => 'Não foi possivel desativar a Categoria.',
                    'icon'        => 'error'
                ]);
            }
        }
    }

    public function restore($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar esta categoria?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->categoria->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Esta Categoria já esta ativa.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->categoria->restore();

                $this->notification([
                    'title'       => 'Categoria restaurada!',
                    'description' => 'Categoria foi restaurada com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('categoriaEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura a Categoria.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.categorias.categoria-edit-modal');
    }
}
