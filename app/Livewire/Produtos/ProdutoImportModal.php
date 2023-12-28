<?php

namespace App\Livewire\Produtos;

use Livewire\Component;
use App\Models\Produtos;
use App\Models\Categorias;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\ProdutosForm;
use Illuminate\Support\Facades\Storage;
use Symfony\Contracts\Service\Attribute\Required;

class ProdutoImportModal extends Component
{
    use Actions;
    use WithFileUploads;

    public $produtoImportModal = false;

    #[Required('nullable|sometimes|file|max:1024')]
    public $arquivo;

    public $resetar_produtos = false;
    public $dados;

    #[\Livewire\Attributes\On('import')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("produtoImportModal")');
    }

    public function updatedArquivo($value)
    {
        dump($value);
        if($value) {
            $arquivo_url = $this->arquivo->store('uploads', 'public', ['disk' => 'public']);
        }
    }

    public function save($params=null)
    {
        $arquivo_url = null;

        if($this->arquivo) {
            $arquivo_url = $this->arquivo->store('uploads', 'public', ['disk' => 'public']);
            $this->arquivo->delete();
            $data['arquivo_url'] = $arquivo_url;
        }

        if($this->arquivo) {
            $this->arquivo->delete();
            Storage::disk('public')->delete($arquivo_url);
        }

        return;

        $this->validate([
            "arquivo" => "required",
        ]);

        $validated = $this->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Importar estes novos produtos?',
                'acceptLabel' => 'Sim, importe',
                'method'      => 'upload',
                'params'      => 'Import',
            ]);
            return;
        }

        // $validated['slug'] = Str::slug($this->form->titulo);
        // $validated['empresas_id'] = auth()->user()->empresas_id;

        try {
            $this->reset('produtoImportModal');
    
            $this->notification([
                'title'       => 'Produtos importados!',
                'description' => 'Produto foram importados com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na importação!',
                'description' => 'Não foi possivel importar os Produtos.',
                'icon'        => 'error'
            ]);
        }
    }

    #[\Livewire\Attributes\On('onCloseProdutoImportModal')]
    public function removeFile()
    {
        if($this->arquivo) {
            $this->arquivo->delete();
            $this->reset('arquivo');
        }
    }

    public function render()
    {
        return view('livewire.produtos.produto-import-modal');
    }
}
