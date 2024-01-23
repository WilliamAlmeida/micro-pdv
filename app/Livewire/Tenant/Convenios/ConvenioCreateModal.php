<?php

namespace App\Livewire\Convenios;

use App\Models\Cidade;
use App\Models\Estado;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use App\Models\Tenant\Convenios;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\Tenant\ConveniosForm;
use App\Http\Controllers\Api\CepController;

class ConvenioCreateModal extends Component
{
    use Actions;

    public ConveniosForm $form;

    public $convenioCreateModal = false;

    public $array_estados;

    public function mount()
    {
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

        $this->js('$openModal("convenioCreateModal")');
    }

    public function save($params=null)
    {
        if($this->form->validateCpfCnpj() && intval($this->form->cpf)) return;

        $this->form->validate([
            "cnpj" => "nullable|unique:convenios,cnpj",
            "cpf" => "nullable|unique:convenios,cpf",
            "razao_social" => "unique:convenios,razao_social",
            "nome_fantasia" => "unique:convenios,nome_fantasia",
        ]);

        $validated = $this->form->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo convênio?',
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
            $convenio = Convenios::create($validated);

            $this->reset('convenioCreateModal');
    
            $this->notification([
                'title'       => 'Convênio registrado!',
                'description' => 'Convênio foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Convênio.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.convenios.convenio-create-modal');
    }
}
