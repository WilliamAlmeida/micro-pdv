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

class ConvenioEditModal extends Component
{
    use Actions;
    use HelperActions;

    public ConveniosForm $form;

    public $convenioEditModal = false;
 
    public Convenios $convenio;

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

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->resetValidation();

        $this->convenio = Convenios::withTrashed()->find($rowId);

        $this->form->mount($this->convenio);

        $this->js('$openModal("convenioEditModal")');
    }

    public function save($params=null)
    {
        if($this->form->validateCpfCnpj()) return;

        $this->form->validate([
            'cnpj' => ['nullable', tenant()->unique('convenios')->ignore($this->convenio)],
            'cpf' => ['nullable', tenant()->unique('convenios')->ignore($this->convenio)],
            'razao_social' => tenant()->unique('convenios')->ignore($this->convenio),
            'nome_fantasia' => tenant()->unique('convenios')->ignore($this->convenio),
        ]);

        if(is_null($this->form->idpais)) $this->form->idpais = 1;

        $validated = $this->form->validate();

        $validated['slug'] = Str::slug($this->form->nome_fantasia);
        $validated['end_uf'] = Estado::find($this->form->idestado)?->id;
        $validated['idcidade'] = Cidade::whereEstadoId($this->form->idestado)->whereNome($this->form->end_cidade)->first()?->id;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Atualizar as informações deste convênio?',
                'acceptLabel' => 'Sim, atualize',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        try {
            $this->convenio->update($validated);

            $this->reset('convenioEditModal');
    
            $this->notification([
                'title'       => 'Convênio atualizado!',
                'description' => 'Convênio foi atualizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha na atualização!',
                'description' => 'Não foi possivel atualizar o Convênio.',
                'icon'        => 'error'
            ]);
        }
    }

    public function delete($params=null)
    {
        if($params == null) {
            if($this->convenio->trashed()) {
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Deletar este convênio?',
                    'acceptLabel' => 'Sim, delete',
                    'method'      => 'delete',
                    'params'      => 'Deleted',
                ]);
            }else{
                $this->dialog()->confirm([
                    'icon'        => 'trash',
                    'title'       => 'Você tem certeza?',
                    'description' => 'Desativar este convênio?',
                    'acceptLabel' => 'Sim, desative',
                    'method'      => 'delete',
                    'params'      => 'Deactivate',
                ]);
            }
            return;
        }

        try {
            if($this->convenio->trashed()) {
                $clientes_count = $this->convenio->clientes()->withTrashed()->count();
                if($clientes_count) {
                    $this->notification([
                        'title'       => 'Falha ao deletar!',
                        'description' => "Esta Convênio está vinculado a {$clientes_count} clientes.",
                        'icon'        => 'error'
                    ]);
                    return;
                }else{
                    $this->convenio->forceDelete();

                    $this->notification([
                        'title'       => 'Convênio deletado!',
                        'description' => 'Convênio foi deletado com sucesso',
                        'icon'        => 'success'
                    ]);
                }
            }else{
                $this->convenio->delete();
                
                $this->notification([
                    'title'       => 'Convênio desativado!',
                    'description' => 'Convênio foi desativado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('convenioEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;

            if($this->convenio->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao deletar!',
                    'description' => 'Não foi possivel deletar o Convênio.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Falha ao desativar!',
                    'description' => 'Não foi possivel desativar o Convênio.',
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
                'description' => 'Restaurar este convênio?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => 'Restored',
            ]);
            return;
        }

        try {
            if(!$this->convenio->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Convênio já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $this->convenio->restore();

                $this->notification([
                    'title'       => 'Convênio restaurado!',
                    'description' => 'Convênio foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('convenioEditModal');

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Convênio.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.convenios.convenio-edit-modal');
    }
}
