<?php

namespace App\Livewire\Produtos;

use Livewire\Component;
use App\Models\Produtos;
use App\Models\Categorias;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\ProdutosForm;
use Illuminate\Support\Facades\Storage;

class ProdutoEditModal extends Component
{
    use Actions;

    public ProdutosForm $form;

    public $produtoEditModal = false;
 
    public Produtos $produto;

    public $array_categorias = [];

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->produto = Produtos::withTrashed()->with('categorias')->find($rowId);

        $this->array_categorias = Categorias::select('id', 'titulo')->get()->toArray();

        $this->form->mount($this->produto);

        $this->js('$openModal("produtoEditModal")');
    }

    public function save($params=null)
    {
        $this->form->validate([
            "titulo" => "unique:produtos,titulo,{$this->produto->id}",
        ]);

        $validated = $this->form->validate();

        if(!$this->form->preco_promocao) {
            $this->form->reset('promocao_inicio', 'promocao_fim');
            $validated = array_merge($validated, $this->form->only('promocao_inicio', 'promocao_fim'));
        }

        $validated['slug'] = Str::slug($this->form->titulo);

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste produto?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->produto->update($validated);

            $this->produto->categorias()->sync(['categorias_id' => $this->form->categoria]);

            $this->reset('produtoEditModal');
    
            $this->notification([
                'title'       => 'Produto atualizado!',
                'description' => 'Produto foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Produto.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            if($this->produto->trashed()) {
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Deletar este produto?',
                    'acceptLabel' => 'Sim, delete',
                    'method'      => 'delete',
                    'params'      => 'Deleted',
                ]);
            }else{
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Desativar este produto?',
                    'acceptLabel' => 'Sim, desative',
                    'method'      => 'delete',
                    'params'      => 'Deactivate',
                ]);
            }
            return;
        }

        try {
            if($this->produto->trashed()) {
                $this->produto->categorias()->detach();

                if($this->produto->allimage) {
                    foreach($this->produto->allimage as $imagem) {
                        Storage::delete($imagem->url);
                        $imagem->delete();
                    }
                }

                $this->produto->forceDelete();

                $this->notification([
                    'title'       => 'Produto deletado!',
                    'description' => 'Produto foi deletado com sucesso',
                    'icon'        => 'success'
                ]);
            }else{
                $this->produto->delete();
                
                $this->notification([
                    'title'       => 'Produto desativado!',
                    'description' => 'Produto foi desativado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('produtoEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;

            if($this->produto->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao deletar!',
                    'description' => 'Não foi possivel deletar o Produto.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Falha ao desativar!',
                    'description' => 'Não foi possivel desativar o Produto.',
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
                'description' => 'Restaurar este produto?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->produto->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Produto já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->produto->restore();

                $this->notification([
                    'title'       => 'Produto restaurado!',
                    'description' => 'Produto foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('produtoEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Produto.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.produtos.produto-edit-modal');
    }
}
