<?php

namespace App\Livewire\Categorias;

use App\Models\Categorias;
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

final class CategoriaTable extends PowerGridComponent
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
        return Categorias::query()->withCount('produtos');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            // ->addColumn('empresas_id')
            ->addColumn('titulo')

           /** Example of custom column using a closure **/
            ->addColumn('titulo_lower', fn (Categorias $model) => strtolower(e($model->titulo)))

            // ->addColumn('slug')
            // ->addColumn('ordem')
            // ->addColumn('file_imagem')
            // ->addColumn('codigo_barras_1')
            // ->addColumn('deleted_at_formatted', fn (Categorias $model) => Carbon::parse($model->deleted_at)->format('d/m/Y H:i:s'))
            ->addColumn('created_at_formatted', fn (Categorias $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'))
            ->addColumn('is_active', fn (Categorias $model) => $model->deleted_at ? 'Sim' : 'Não')
            ->addColumn('produtos_count');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            // Column::make('Empresas id', 'empresas_id'),
            Column::make('Titulo', 'titulo')
                ->sortable()
                ->searchable(),

            // Column::make('Slug', 'slug')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Ordem', 'ordem'),
            // Column::make('File imagem', 'file_imagem'),
            // Column::make('Codigo barras 1', 'codigo_barras_1')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Produtos', 'produtos_count')
            ->sortable(),

            // Column::make('Deleted at', 'deleted_at_formatted', 'deleted_at'),
            Column::make('Desativado?', 'is_active', 'deleted_at')
                ->sortable('deleted_at'),

                Column::make('Registrado em', 'created_at_formatted', 'created_at')
                    ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('titulo')->operators(['contains']),
            // Filter::inputText('slug')->operators(['contains']),
            // Filter::inputText('codigo_barras_1')->operators(['contains']),
            // Filter::datetimepicker('deleted_at'),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('restore')]
    public function restore($rowId, $params=null): void
    {
        $categoria = Categorias::withTrashed()->findOrFail($rowId);

        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar esta categoria?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => [$rowId, 'Restored'],
            ]);
            return;
        }

        try {
            if(!$categoria->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Esta Categoria já esta ativa.',
                    'icon'        => 'error'
                ]);
            }else{
                $categoria->restore();

                $this->notification([
                    'title'       => 'Categoria restaurada!',
                    'description' => 'Categoria foi restaurada com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura a Categoria.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(\App\Models\Categorias $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('restore')
                ->slot('Restaurar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('restore', ['rowId' => $row->id])
                ->can($row->trashed()),
            // Button::add('restore-icon')
            //     ->can($row->trashed())
            //     ->render(function ($row) {
            //         return \Blade::render(<<<HTML
            //             <x-button class="pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700"
            //             icon="refresh" label="Restaurar" wire:click="restore($row->id)"
            //             />
            //         HTML);
            // })
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
