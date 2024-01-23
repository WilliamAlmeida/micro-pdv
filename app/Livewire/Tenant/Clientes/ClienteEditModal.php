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

class ClienteEditModal extends Component
{
    use Actions;

    public ClientesForm $form;

    public $clienteEditModal = false;
 
    public Clientes $cliente;

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

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->cliente = Clientes::withTrashed()->find($rowId);

        $this->form->mount($this->cliente);

        $this->array_convenios = Convenios::select('id', 'nome_fantasia')->get()->toArray();

        $this->js('$openModal("clienteEditModal")');
    }

    public function save($params=null)
    {
        if($this->form->validateCpfCnpj()) return;

        $this->form->validate([
            "cnpj" => "nullable|unique:clientes,cnpj,{$this->cliente->id}",
            "cpf" => "nullable|unique:clientes,cpf,{$this->cliente->id}",
            "razao_social" => "unique:clientes,razao_social,{$this->cliente->id}",
            "nome_fantasia" => "unique:clientes,nome_fantasia,{$this->cliente->id}",
        ]);

        $validated = $this->form->validate();

        $validated['slug'] = Str::slug($this->form->nome_fantasia);
        $validated['end_uf'] = Estado::find($this->form->idestado)?->id;
        $validated['idcidade'] = Cidade::whereEstadoId($this->form->idestado)->whereNome($this->form->end_cidade)->first()?->id;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste cliente?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->cliente->update($validated);

            $this->reset('clienteEditModal');
    
            $this->notification([
                'title'       => 'Cliente atualizado!',
                'description' => 'Cliente foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Cliente.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            if($this->cliente->trashed()) {
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Deletar este cliente?',
                    'acceptLabel' => 'Sim, delete',
                    'method'      => 'delete',
                    'params'      => 'Deleted',
                ]);
            }else{
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Desativar este cliente?',
                    'acceptLabel' => 'Sim, desative',
                    'method'      => 'delete',
                    'params'      => 'Deactivate',
                ]);
            }
            return;
        }

        try {
            if($this->cliente->trashed()) {
                $this->cliente->forceDelete();

                $this->notification([
                    'title'       => 'Cliente deletado!',
                    'description' => 'Cliente foi deletado com sucesso',
                    'icon'        => 'success'
                ]);
            }else{
                $this->cliente->delete();
                
                $this->notification([
                    'title'       => 'Cliente desativado!',
                    'description' => 'Cliente foi desativado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('clienteEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;

            if($this->cliente->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao deletar!',
                    'description' => 'Não foi possivel deletar o Cliente.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Falha ao desativar!',
                    'description' => 'Não foi possivel desativar o Cliente.',
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
                'description' => 'Restaurar este cliente?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->cliente->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Cliente já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->cliente->restore();

                $this->notification([
                    'title'       => 'Cliente restaurado!',
                    'description' => 'Cliente foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('clienteEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Cliente.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.clientes.cliente-edit-modal');
    }
}
