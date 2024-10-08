<?php

namespace App\Livewire\Tenant\Convenios;

use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Tenant\Convenios;
use WireUi\Traits\Actions;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ConvenioTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;

    public function setUp(): array
    {
        // $this->showCheckBox();
        $this->softDeletes('withTrashed');

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns()->showSoftDeletes(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Convenios::query()->with('pais', 'estado', 'cidade')
        ->select('convenios.*')->withCount('clientes')
        ->selectRaw('CONCAT(cnpj, cpf) as cnpj_cpf');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('nome_fantasia_capitalize', fn (Convenios $model) => ucwords(strtolower(e($model->nome_fantasia))))
            ->addColumn('razao_social_capitalize', fn (Convenios $model) => ucwords(strtolower(e($model->razao_social))))
            ->addColumn('cnpj_cpf_formatted', fn(Convenios $model) => ($model->cnpj && $model->cpf) ? $model->cnpj.'<br/>'.$model->cpf : ($model->cnpj ?: $model->cpf))
            ->addColumn('endereco', function (Convenios $model) {   
                $endereco = ucwords(strtolower($model->end_logradouro)).', '.$model->end_numero.'<br>'.ucwords(strtolower($model->end_bairro)).', '.ucwords(strtolower($model->end_cidade));
                if($model->estado) $endereco .= ' - '.$model->estado->uf;
                return strlen($endereco) < 10 ? null : $endereco;
            })
            ->addColumn('clientes_count', fn(Convenios $model) => $model->clientes_count)
            ->addColumn('created_at_formatted', fn (Convenios $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nome Fantasia', 'nome_fantasia_capitalize', 'nome_fantasia')
                ->sortable()
                ->searchable(),

            Column::make('Razao Social', 'razao_social_capitalize', 'razao_social')
                ->sortable()
                ->searchable()
                ->hidden(true, false),

            Column::make('Cnpj/Cpf', 'cnpj_cpf_formatted', 'cnpj_cpf')
            ->sortable(),
            Column::make('Endereço', 'endereco')
            ->searchable(),

            Column::make('Clientes', 'clientes_count')
            ->sortable(),

            Column::make('CNPJ', 'cnpj')->searchable()->hidden(),
            Column::make('CPF', 'cpf')->searchable()->hidden(),
            Column::make('Estado', 'idestado')->searchable()->hidden(),
            Column::make('Cidade', 'idcidade')->searchable()->hidden(),
            Column::make('Logradouro', 'end_logradouro')->searchable()->hidden(),
            Column::make('Bairro', 'end_bairro')->searchable()->hidden(),

            Column::make('Registrado em', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nome_fantasia')->operators(['contains']),
            Filter::inputText('razao_social')->operators(['contains']),
            Filter::inputText('cnpj')->operators(['contains']),
            Filter::inputText('cpf')->operators(['contains']),
            Filter::inputText('end_logradouro')->operators(['contains']),
            Filter::inputText('end_bairro')->operators(['contains']),
            Filter::inputText('end_uf')->operators(['contains']),
            Filter::select('idestado')
            ->dataSource(Estado::all())
            ->optionValue('id')
            ->optionLabel('uf'),
            Filter::select('idcidade')
            ->dataSource(Cidade::has('convenios')->select('id', 'nome')->get())
            ->optionValue('id')
            ->optionLabel('nome'),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('restore')]
    public function restore($rowId, $params=null): void
    {
        $convenio = Convenios::withTrashed()->findOrFail($rowId);
        
        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar este convênio?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => [$rowId, 'Restored'],
            ]);
            return;
        }
        
        try {
            if(!$convenio->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Convênio já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $convenio->restore();
                
                $this->notification([
                    'title'       => 'Convênio restaurado!',
                    'description' => 'Convênio foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }
            
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

    public function actions(Convenios $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Editar')
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('edit')
            ->slot('Editar')
            ->bladeComponent('button', ['icon' => 'pencil'])
            ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            ->id()
            ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('restore')
            ->slot('Editar')
            ->bladeComponent('button', ['icon' => 'refresh'])
            ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            ->id()
            ->dispatch('restore', ['rowId' => $row->id])
            ->can($row->trashed()),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
