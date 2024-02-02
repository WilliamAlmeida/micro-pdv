<?php

namespace App\Livewire\Admin\Empresas;

use App\Models\Estado;
use App\Models\Tenant;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\EmpresaForm;
use App\Http\Controllers\Api\CepController;
use App\Http\Controllers\Api\CnpjController;
use App\Traits\HelperActions;

#[Layout('components.layouts.admin')]
class EmpresaCreate extends Component
{
    use Actions;
    use HelperActions;

    public EmpresaForm $form;

    public $array_tipos_empresas;
    public $array_estados;

    #[Locked]
    public $array_days_of_week = [
        'Mon' => 'Segunda-feira',
        'Tue' => 'Terça-feira',
        'Wed' => 'Quarta-feira',
        'Thu' => 'Quinta-feira',
        'Fri' => 'Sexta-feira',
        'Sat' => 'Sábado',
        'Sun' => 'Domingo',
    ];

    public function mount()
    {
        $this->array_tipos_empresas = Tenant::$tipos_empresas;
        $this->array_estados = Estado::select('id','uf')->get()->toArray();

        $this->form->mount(null);
    }

    public function updated($name, $value) 
    {
        if($name == 'form.nome_fantasia') {
            $this->form->slug = Str::slug($value);
            if(empty($this->form->razao_social)) $this->form->razao_social = Str::upper($value);
        }
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

    public function add_hours($day_of_week)
    {
        $this->form->add_hours($day_of_week);
    }

    public function remove_hours($day_of_week, $index)
    {
        $this->form->remove_hours($day_of_week, $index);
    }

    public function save($params=null)
    {
        $validated = $this->form->validate();

        $this->form->validate([
            "cnpj" => "unique:tenants,cnpj",
            "razao_social" => "unique:tenants,razao_social",
            "nome_fantasia" => "unique:tenants,nome_fantasia",
        ]);

        if($this->form->validateHours($this)) return;

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Salvar os dados da empresa?',
                'acceptLabel' => 'Sim, salve',
                'method'      => 'save',
                'params'      => 'Saved',
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            if(is_null($this->form->idpais)) $this->form->idpais = 1;

            $empresa = Tenant::create($this->form->all());

            $empresa->users()->attach(auth()->user());

            $horarios = $this->form->getHours();
            if($horarios) $empresa->horarios()->createMany($horarios);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Dados da Empresa foram salvo com sucesso.',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->dialog()->confirm([
                'title'       => 'Deseja acessar o Painel da Empresa?',
                // 'description' => 'Deseja alterar o preço?',
                'icon'        => 'question',
                'accept'      => [
                    'label'  => 'Sim',
                    'method' => 'saved',
                    'params' => [$empresa->id],
                ],
                'onClose' => [
                    'method' => 'saved',
                ]
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
    
            $this->notification([
                'title'       => 'Falha no processo!',
                'description' => 'Não foi possivel salvar os dados da Empresa.',
                'icon'        => 'error'
            ]);
        }
    }

    public function saved($params=null)
    {
        if($params) {
            $this->redirect(route('tenant.dashboard', $params), true);
        }else{
            $this->redirect(route('admin.empresas.index'), true);
        }
    }

    public function render()
    {
        return view('livewire.admin.empresas.empresa-create');
    }
}
