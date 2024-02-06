<?php

namespace App\Livewire\Admin\Permissions;

use App\Traits\HelperActions;
use Spatie\Permission\Models\Permission;
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

final class PermissionTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;
    use HelperActions;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showToggleColumns()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Permission::query()->withCount('users', 'roles');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            // ->addColumn('guard_name')
            ->addColumn('name')
            // ->addColumn('used_where', function (Permission $model) {
            //     return is_null($model->tenant_id) ? 'global' : $model->tenant_id;
            // })
            ->addColumn('users_count')
            ->addColumn('roles_count')
            ->addColumn('name')
            ->addColumn('created_at_formatted', fn (Permission $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            // Column::make('Guard', 'guard_name'),
            // Column::make('Uso', 'used_where')->sortable()->searchable(),
            Column::make('Permissão', 'name')->sortable()->searchable(),
            Column::make('Usuários', 'users_count')->sortable(),
            Column::make('Funções', 'roles_count')->sortable(),
            Column::make('Registrado em', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, $params=null): void
    {
        $role = Permission::findOrFail($rowId);

        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Deletar esta permissão?',
                'acceptLabel' => 'Sim, delete',
                'method'      => 'delete',
                'params'      => [$rowId, 'Delete'],
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        try {
            $role->delete();

            $this->notification([
                'title'       => 'Permissão deletada!',
                'description' => 'Permissão foi deletada com sucesso',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao deletar!',
                'description' => 'Não foi possivel deletar a Permissão.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(\Spatie\Permission\Models\Permission $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->bladeComponent('button', ['icon' => 'pencil'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->can(auth()->user()->isAdmin())
                ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Deletar')
                ->bladeComponent('button', ['icon' => 'trash'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->can(auth()->user()->isAdmin())
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }

    public function header(): array
    {
        // Selecionados (<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)
        return [
            Button::add('bulk-delete')
                ->slot(__('Deletar'))
                ->bladeComponent('button', ['icon' => 'trash', 'label' => __('Deletar')])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
                dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700')
                // ->openModal('admin.permissions.permission-index', [])
                ->dispatch('bulkToggleDeleteEvent', []),
        ];
    }

    protected function getListeners()
    {
        return array_merge(parent::getListeners(), ['eventX','eventY','bulkToggleDeleteEvent']);
    }

    public function bulkToggleDeleteEvent(): void
    {
        if(count($this->checkboxValues) == 0) return;

        Permission::whereIn('id', $this->checkboxValues)->whereNot('id', auth()->id())->delete();

        $this->notification([
            'title' => 'Permissões deletadas!',
            'description' => 'Permissões deletadas com sucesso.',
            'icon' => 'success',
        ]);
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
