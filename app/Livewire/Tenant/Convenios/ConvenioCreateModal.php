<?php

namespace App\Livewire\Tenant\Convenios;

use App\Models\Cidade;
use App\Models\Estado;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use App\Traits\HelperActions;
use App\Models\Tenant\Convenios;
use App\Http\Controllers\Api\CepController;
use App\Http\Controllers\Api\CnpjController;
use App\Livewire\Forms\Tenant\ConveniosForm;

class ConvenioCreateModal extends Component
{
    use Actions;
    use HelperActions;

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

        if(empty($cep)) {
            $this->set_focus(['query' => '[name="form.end_cep"]']);
            return;
        }

        $helper = new CepController;
        $response = json_decode($helper->show($cep));

        if($response->status == 'ERROR') {
            $this->set_focus(['query' => '[name="form.end_cep"]']);

            $this->dialog()->error(
                $title = 'Error!!!',
                $description = $response->message
            );
            return;
        }

        $this->form->fillCep($response);

        $this->notification()->success(
            $title = 'Cep Encontrado',
            $description = "Busca pelo CEP {$this->form->end_cep} foi finalizada!"
        );

        $this->set_focus(['query' => '[name="form.end_numero"]']);
        $this->form->resetValidation();
    }

    public function pesquisar_cnpj()
    {
        $cnpj = preg_replace( '/[^0-9]/', '', $this->form->cnpj);

        if(empty($cnpj)) {
            $this->set_focus(['query' => '[name="form.cnpj"]']);
            return;
        }

        $helper = new CnpjController;
        $response = json_decode($helper->show($cnpj));

        if($response->status == 'ERROR') {
            $this->set_focus(['query' => '[name="form.cnpj"]']);

            $this->dialog()->error(
                $title = 'Error!!!',
                $description = $response->message
            );
            return;
        }

        $this->form->fillCnpj($response);

        $this->notification()->success(
            $title = 'CNPJ Encontrado',
            $description = "Busca pelo CNPJ {$this->form->cnpj} foi finalizada!"
        );

        $this->set_focus(['query' => '[name="form.end_cep"]']);
        $this->form->resetValidation();
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
            'cnpj' => ['nullable', tenant()->unique('convenios')],
            'cpf' => ['nullable', tenant()->unique('convenios')],
            'razao_social' => tenant()->unique('convenios'),
            'nome_fantasia' => tenant()->unique('convenios'),
        ]);

        if(is_null($this->form->idpais)) $this->form->idpais = 1;

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
        return view('livewire.tenant.convenios.convenio-create-modal');
    }
}
