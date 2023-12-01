<?php

namespace App\Livewire\Fornecedores;

use App\Models\Cidade;
use App\Models\Estado;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use App\Models\Fornecedores;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\FornecedoresForm;
use App\Http\Controllers\Api\CepController;

class FornecedorCreateModal extends Component
{
    use Actions;

    public FornecedoresForm $form;

    public $fornecedorCreateModal = false;

    public $array_tipos_fornecedores;
    public $array_estados;

    public function mount()
    {
        $this->array_tipos_fornecedores = Fornecedores::$tipos_empresas;
        $this->array_estados = Estado::select('id','uf')->get()->toArray();
    }

    public function pesquisar_cep()
    {
        $cep = preg_replace( '/[^0-9]/', '', $this->form->end_cep);

        if(empty($cep)) return;

        if(strlen($cep) != 8) {
            $this->dialog()->error(
                $title = 'Error!!!',
                $description = 'CEP invalido!'
            );
            return;
        }

        $helper = new CepController;
        $response = json_decode($helper->show($cep));

        $this->form->fillCep($response);

        $this->notification()->success(
            $title = 'Cep Encontrado',
            $description = "Busca pelo CEP {$this->form->end_cep} foi finalizada!"
        );
    }

    #[\Livewire\Attributes\On('create')]
    public function create(): void
    {
        $this->resetValidation();

        $this->form->reset();

        $this->js('$openModal("fornecedorCreateModal")');
    }

    public function save($params=null)
    {
        if(empty($this->form->cnpj) && empty($this->form->cpf)) {
            $this->addError('form.cnpj', 'Preencha o CPF ou CNPJ.');
            $this->addError('form.cpf', 'Preencha o CPF ou CNPJ.');
            return;
        }

        $this->form->validate([
            "cnpj" => "unique:fornecedores,cnpj",
            "razao_social" => "unique:fornecedores,razao_social",
            "nome_fantasia" => "unique:fornecedores,nome_fantasia",
        ]);
        
        $validated = $this->form->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo fornecedor?',
                'acceptLabel' => 'Sim, registre',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        $validated['slug'] = Str::slug($this->form->nome_fantasia);
        $validated['end_uf'] = Estado::find($this->form->idestado)?->id;
        $validated['idcidade'] = Cidade::whereEstadoId($this->form->idestado)->whereNome($this->form->end_cidade)->first()?->id;
        $validated['empresas_id'] = auth()->user()->empresas_id;

        try {
            $fornecedor = Fornecedores::create($validated);

            $this->reset('fornecedorCreateModal');
    
            $this->notification([
                'title'       => 'Fornecedor registrado!',
                'description' => 'Fornecedor foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Fornecedor.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.fornecedores.fornecedor-create-modal');
    }
}
