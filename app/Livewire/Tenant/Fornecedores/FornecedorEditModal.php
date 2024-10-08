<?php

namespace App\Livewire\Tenant\Fornecedores;

use App\Models\Cidade;
use App\Models\Estado;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use App\Traits\HelperActions;
use App\Models\Tenant\Fornecedores;
use App\Http\Controllers\Api\CepController;
use App\Http\Controllers\Api\CnpjController;
use App\Livewire\Forms\Tenant\FornecedoresForm;

class FornecedorEditModal extends Component
{
    use Actions;
    use HelperActions;

    public FornecedoresForm $form;

    public $fornecedorEditModal = false;
 
    public Fornecedores $fornecedor;

    public $array_tipos_fornecedores;
    public $array_estados;

    public function mount()
    {
        $this->array_tipos_fornecedores = Fornecedores::$tipos_fornecedores;
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

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->fornecedor = Fornecedores::withTrashed()->find($rowId);

        $this->form->mount($this->fornecedor);

        $this->js('$openModal("fornecedorEditModal")');
    }

    public function save($params=null)
    {
        if($this->form->validateCpfCnpj()) return;

        $this->form->validate([
            'cnpj' => ['nullable', tenant()->unique('fornecedores')->ignore($this->fornecedor)],
            'cpf' => ['nullable', tenant()->unique('fornecedores')->ignore($this->fornecedor)],
            'razao_social' => tenant()->unique('fornecedores')->ignore($this->fornecedor),
            'nome_fantasia' => tenant()->unique('fornecedores')->ignore($this->fornecedor),
        ]);

        if(is_null($this->form->idpais)) $this->form->idpais = 1;

        $validated = $this->form->validate();

        $validated['slug'] = Str::slug($this->form->nome_fantasia);
        $validated['end_uf'] = Estado::find($this->form->idestado)?->id;
        $validated['idcidade'] = Cidade::whereEstadoId($this->form->idestado)->whereNome($this->form->end_cidade)->first()?->id;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste fornecedor?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->fornecedor->update($validated);

            $this->reset('fornecedorEditModal');
    
            $this->notification([
                'title'       => 'Fornecedor atualizado!',
                'description' => 'Fornecedor foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Fornecedor.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            if($this->fornecedor->trashed()) {
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Deletar este fornecedor?',
                    'acceptLabel' => 'Sim, delete',
                    'method'      => 'delete',
                    'params'      => 'Deleted',
                ]);
            }else{
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Desativar este fornecedor?',
                    'acceptLabel' => 'Sim, desative',
                    'method'      => 'delete',
                    'params'      => 'Deactivate',
                ]);
            }
            return;
        }

        try {
            if($this->fornecedor->trashed()) {
                $this->fornecedor->forceDelete();

                $this->notification([
                    'title'       => 'Fornecedor deletado!',
                    'description' => 'Fornecedor foi deletado com sucesso',
                    'icon'        => 'success'
                ]);
            }else{
                $this->fornecedor->delete();
                
                $this->notification([
                    'title'       => 'Fornecedor desativado!',
                    'description' => 'Fornecedor foi desativado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('fornecedorEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;

            if($this->fornecedor->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao deletar!',
                    'description' => 'Não foi possivel deletar o Fornecedor.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Falha ao desativar!',
                    'description' => 'Não foi possivel desativar o Fornecedor.',
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
                'description' => 'Restaurar este fornecedor?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->fornecedor->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Fornecedor já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->fornecedor->restore();

                $this->notification([
                    'title'       => 'Fornecedor restaurado!',
                    'description' => 'Fornecedor foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('fornecedorEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Fornecedor.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.fornecedores.fornecedor-edit-modal');
    }
}
