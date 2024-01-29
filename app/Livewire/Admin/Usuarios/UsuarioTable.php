<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
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

final class UsuarioTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;

    public function setUp(): array
    {
        // $this->showCheckBox();
        $this->softDeletes('withTrashed');

        // $this->persist(['columns', 'filters']); 

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showToggleColumns()->showSoftDeletes(), // ->includeViewOnTop('components.datatable.admin.usuarios-header-top')->showSearchInput()
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('type', function (User $user) {
                return $user->getTypeUser();
            })

           /** Example of custom column using a closure **/
            ->addColumn('name_lower', fn (User $model) => strtolower(e($model->name)))

            ->addColumn('email')
            ->addColumn('created_at_formatted', fn (User $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'))
            ->addColumn('deleted_at_formatted', fn (User $model) => Carbon::parse($model->deleted_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            
            Column::make('Tipo', 'type')
            ->sortable(),

            Column::make('Nome', 'name')
                ->sortable()
                ->searchable(),

            Column::make('E-mail', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Desativado?', 'deleted_at_formatted', 'deleted_at')
                ->sortable(),

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
            // Filter::select('is_admin')
            // ->dataSource(User::listTypeUser())
            // ->optionValue('type')
            // ->optionLabel('label'),

            /* Mais rapido */
            Filter::boolean('type')
            ->label('Empresa', 'Usuário'),
            // ->builder(function (Builder $query, string $value) {
            //     return $query->where('is_admin', $value === 'true' ? 1 : 0);
            // }),
        ];
    }

    #[\Livewire\Attributes\On('restore')]
    public function restore($rowId, $params=null): void
    {
        $usuario = User::withTrashed()->findOrFail($rowId);

        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar este usuário?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => [$rowId, 'Restored'],
            ]);
            return;
        }

        try {
            if(!$usuario->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Usuário já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $usuario->restore();

                $this->notification([
                    'title'       => 'Usuário restaurado!',
                    'description' => 'Usuário foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Usuário.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(\App\Models\User $row): array
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
                ->can(auth()->user()->is_admin)
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

    // public function header(): array
    // {
    //     // Selecionados (<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)
    //     return [
    //         Button::add('bulk-restore')
    //             ->slot(__('Restaurar'))
    //             ->bladeComponent('button', ['icon' => 'refresh', 'label' => __('Restaurar')])
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    //             dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700')
    //             ->dispatch('bulkToggleDeleteEvent', [true]),
    //         Button::add('bulk-delete')
    //             ->slot(__('Desativar'))
    //             ->bladeComponent('button', ['icon' => 'x', 'label' => __('Desativar')])
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    //             dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700')
    //             ->dispatch('bulkToggleDeleteEvent', [false])
    //     ];
    // }

    // protected function getListeners()
    // {
    //     return array_merge(parent::getListeners(), ['eventX','eventY','bulkToggleDeleteEvent']);
    // }

    // public function bulkToggleDeleteEvent($action=null): void
    // {
    //     if(count($this->checkboxValues) == 0) return;

    //     if($action === true) {
    //         User::whereIn('id', $this->checkboxValues)->whereNot('id', auth()->id())->restore();

    //         $this->notification([
    //             'title' => 'Usuários restaurados!',
    //             'description' => 'Usuários foram ativados.',
    //             'icon' => 'success',
    //         ]);
    //     }else if($action === false) {
    //         User::whereIn('id', $this->checkboxValues)->whereNot('id', auth()->id())->delete();

    //         $this->notification([
    //             'title' => 'Usuários desativados!',
    //             'description' => 'Usuários desativados.',
    //             'icon' => 'success',
    //         ]);
    //     }
    // }

    // public function actionRules($row): array
    // {
    //    return [
    //         // Hide button edit for ID 1
    //         Rule::button('edit')
    //             ->when(fn($row) => $row->id === 1)
    //             ->hide(),
    //     ];
    // }
}
