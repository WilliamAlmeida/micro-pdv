<?php

namespace App\Livewire\Tenant\Clientes;

use App\Models\Cidade;
use App\Models\Estado;
use Livewire\Component;
use App\Models\Tenant\Clientes;
use App\Models\Tenant\Convenios;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\Tenant\ClientesForm;
use App\Http\Controllers\Api\CepController;

class ClienteCreateModal extends Component
{
    use Actions;

    public ClientesForm $form;

    public $clienteCreateModal = false;

    public $array_convenios;
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

        $this->array_convenios = Convenios::select('id', 'nome_fantasia')->get()->toArray();
        
        $this->js('$openModal("clienteCreateModal")');
    }

    public function save($params=null)
    {
        if($this->form->validateCpfCnpj() && intval($this->form->cpf)) return;

        $this->form->validate([
            "cnpj" => "nullable|unique:clientes,cnpj",
            "cpf" => "nullable|unique:clientes,cpf",
            "razao_social" => "unique:clientes,razao_social",
            "nome_fantasia" => "unique:clientes,nome_fantasia",
        ]);

        if(is_null($this->form->idpais)) $this->form->idpais = 1;
        $validated = $this->form->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Registrar este novo cliente?',
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
            $cliente = Clientes::create($validated);

            $this->reset('clienteCreateModal');
    
            $this->notification([
                'title'       => 'Cliente registrado!',
                'description' => 'Cliente foi registrado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha no cadastro!',
                'description' => 'Não foi possivel registrar o Cliente.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.clientes.cliente-create-modal');
    }
}
