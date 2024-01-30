<?php

namespace App\Livewire\Tenant\Usuarios;

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

           /** Example of custom column using a closure **/
            ->addColumn('name_lower', fn (User $model) => strtolower(e($model->name)))

            ->addColumn('email')
            ->addColumn('created_at_formatted', fn (User $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            
            Column::make('Nome', 'name')
                ->sortable()
                ->searchable(),

            Column::make('E-mail', 'email')
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
            // Filter::select('is_admin')
            // ->dataSource(User::listTypeUser())
            // ->optionValue('type')
            // ->optionLabel('label'),
        ];
    }

    #[\Livewire\Attributes\On('unlink')]
    public function unlink($rowId, $params=null): void
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Desvincular este usuário da sua Empresa?<br/>Para que ele volta a trabalhar com você, precisará enviar um convite.',
                'acceptLabel' => 'Sim, desvincule',
                'method'      => 'unlink',
                'params'      => [$rowId, 'Confirm'],
            ]);
            return;
        }

        $usuario = User::with('tenants')->find($rowId);

        $linked = $usuario->tenants->firstWhere('id', tenant('id'));

        if(!$linked) return;

        $usuario->tenants()->detach(tenant());

        $this->notification([
            'title'       => 'Usuário desvinculado!',
            'description' => 'Usuário foi desvinculado com sucesso.',
            'icon'        => 'success'
        ]);
    }

    public function actions(User $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Editar')
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('unlink')
                ->slot('Desvincular')
                ->bladeComponent('button', ['icon' => 'trash'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->can((auth()->user()->isAdmin() || auth()->user()->isEmpresa()) && auth()->id() != $row->id)
                ->dispatch('unlink', ['rowId' => $row->id]),
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
