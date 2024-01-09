<?php

namespace App\Livewire\Empresa;

use App\Models\Estado;
use Livewire\Component;
use App\Models\Empresas;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use App\Livewire\Forms\EmpresaForm;
use App\Http\Controllers\Api\CepController;

class EmpresaEdit extends Component
{
    use Actions;

    public EmpresaForm $form;

    public $empresa;

    public $readMode = true;

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
        $this->array_tipos_empresas = Empresas::$tipos_empresas;
        $this->array_estados = Estado::select('id','uf')->get()->toArray();

        $this->empresa = auth()->user()->empresa ?? null;
        $this->form->mount($this->empresa);
    }

    public function updated($name, $value) 
    {
        if($name == 'form.nome_fantasia') $this->form->slug = Str::slug($value);
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

        if(!$this->empresa) {
            $this->form->validate([
                "cnpj" => "unique:empresas,cnpj",
                "razao_social" => "unique:empresas,razao_social",
                "nome_fantasia" => "unique:empresas,nome_fantasia",
            ]);
        }else{
            $this->form->validate([
                "cnpj" => "unique:empresas,cnpj,{$this->empresa->id}",
                "razao_social" => "unique:empresas,razao_social,{$this->empresa->id}",
                "nome_fantasia" => "unique:empresas,nome_fantasia,{$this->empresa->id}",
            ]);
        }

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

        try {
            if(!$this->empresa) {
                $this->empresa = new Empresas($this->form->all());
                $this->empresa->save();

                auth()->user()->update(['empresas_id' => $this->empresa->id]);
            }else{
                $this->empresa->update($this->form->all());
                $this->empresa->horarios()->delete();
            }

            $horarios = $this->form->getHours();
            if($horarios) $this->empresa->horarios()->createMany($horarios);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Dados da Empresa foram salvo com sucesso.',
                'icon'        => 'success'
            ]);

            $this->cancel();
        } catch (\Throwable $th) {
            // throw $th;
    
            $this->notification([
                'title'       => 'Falha no processo!',
                'description' => 'Não foi possivel salvar os dados da Empresa.',
                'icon'        => 'error'
            ]);
        }
    }

    public function cancel()
    {
        $this->reset('readMode');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.empresa.empresa-edit');
    }
}
