<?php

namespace App\Livewire\Admin\Empresas;

use App\Models\Tenant;
use App\Traits\HelperActions;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use WireUi\Traits\Actions;

final class EmpresaTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;
    use HelperActions;

    public function setUp(): array
    {
        // $this->showCheckBox();

        // $this->persist(['columns', 'filters']); 

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showToggleColumns(), // ->showSearchInput()
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Tenant::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('nome_fantasia')
            ->addColumn('id_tipo_empresa', function (Tenant $empresa) {
                return $empresa->getTipoEmpresa()['name'];
            })

           /** Example of custom column using a closure **/
            // ->addColumn('nome_fantasia', fn (Tenant $model) => strtolower(e($model->name)))

            ->addColumn('created_at_formatted', fn (Tenant $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            // Column::make('Id', 'id'),
            
            Column::make('Tipo', 'id_tipo_empresa')
            ->sortable(),

            Column::make('Nome', 'nome_fantasia')
                ->sortable()
                ->searchable(),

            Column::make('Registrado em', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
            Filter::datetimepicker('created_at'),

            /* Mais lento */
            Filter::select('id_tipo_empresa')
            ->dataSource(Tenant::$tipos_empresas)
            ->optionValue('id')
            ->optionLabel('name'),

            /* Mais rapido */
            // Filter::boolean('type')
            // ->label('Empresa', 'Usuário'),
            // ->builder(function (Builder $query, string $value) {
            //     return $query->where('is_admin', $value === 'true' ? 1 : 0);
            // }),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, $params=null): void
    {
        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Deletar esta empresa?<br/>Uma vez deletada, todos os dados seram apagados.',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => [$rowId, 'Deleted'],
            ]);
            
            $this->set_focus(['button' => 'cancel']);
            return;
        }

        try {
            $tenant = Tenant::findOrFail($rowId);

            $tenant->delete();

            $this->notification([
                'title'       => 'Empresa deletada!',
                'description' => 'Empresa foi deletada com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar a Empresa.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(Tenant $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Editar')
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('edit')
                ->slot('Editar')
                ->bladeComponent('button', ['icon' => 'login'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->can(auth()->user()->isAdmin())
                ->route('tenant.dashboard', [$row->id]),
            Button::add('delete')
                ->slot('Deletar')
                ->bladeComponent('button', ['icon' => 'trash'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->can(auth()->user()->isAdmin())
                ->dispatch('delete', [$row->id]),
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
