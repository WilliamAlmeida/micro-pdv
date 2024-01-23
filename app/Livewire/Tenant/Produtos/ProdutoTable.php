<?php

namespace App\Livewire\Produtos;

use App\Models\Tenant\Categorias;
use App\Models\Tenant\Produtos;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
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

final class ProdutoTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;
    
    #[Url('search')]
    public string $search = '';

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
        return Produtos::query()->withTenant()
        ->leftJoin('produtos_has_categorias','produtos_has_categorias.produtos_id','produtos.id')
        ->leftJoin('categorias','categorias.id','produtos_has_categorias.categorias_id')
        ->select('produtos.*', 'categorias.id as categoria_id', 'categorias.titulo as categoria_titulo')
        ;
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
            ->addColumn('titulo_lower', fn (Produtos $model) => strtolower(e($model->titulo)))

            // ->addColumn('categoria', fn (Produtos $model) => $model->categorias->first()->titulo ?? '')
            ->addColumn('categoria_titulo')

            // ->addColumn('slug')
            ->addColumn('descricao')
            // ->addColumn('codigo_barras_1')
            // ->addColumn('codigo_barras_2')
            // ->addColumn('codigo_barras_3')
            ->addColumn('preco_varejo')
            ->addColumn('preco_varejo_formatted', fn (Produtos $model) => 'R$ '.number_format($model->preco_varejo, 2, ',', '.'))
            // ->addColumn('preco_atacado')
            // ->addColumn('valor_garcom')
            ->addColumn('preco_promocao')
            ->addColumn('preco_promocao_formatted', fn (Produtos $model) => 'R$ '.number_format($model->preco_promocao, 2, ',', '.'))
            // ->addColumn('promocao_inicio_formatted', fn (Produtos $model) => Carbon::parse($model->promocao_inicio)->format('d/m/Y'))
            // ->addColumn('promocao_fim_formatted', fn (Produtos $model) => Carbon::parse($model->promocao_fim)->format('d/m/Y'))
            // ->addColumn('trib_icms')
            // ->addColumn('trib_csosn')
            // ->addColumn('trib_cst')
            // ->addColumn('trib_origem_produto')
            // ->addColumn('trib_cfop_de')
            // ->addColumn('trib_cfop_fe')
            // ->addColumn('trib_ncm')
            // ->addColumn('trib_cest')
            ->addColumn('estoque_atual', fn (Produtos $model) => number_format($model->estoque_atual, 2, ',', '.'))
            // ->addColumn('unidade_medida')
            // ->addColumn('codigo_externo')
            ->addColumn('destaque', fn (Produtos $model) => $model->destaque ? 'Sim' : 'Não')
            ->addColumn('deleted_at_formatted', fn (Produtos $model) => Carbon::parse($model->deleted_at)->format('d/m/Y H:i:s'))
            // ->addColumn('views')
            // ->addColumn('somente_mesa')
            // ->addColumn('ordem')
            ->addColumn('created_at_formatted', fn (Produtos $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'))
            ->addColumn('is_active', fn (Produtos $model) => $model->deleted_at ? 'Sim' : 'Não')
            ;
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            // Column::make('Empresas id', 'empresas_id'),
            Column::make('Titulo', 'titulo')
                ->sortable()
                ->searchable(),

            Column::make('Categoria', 'categoria_titulo')
                ->sortable()
                ->searchable(),

            // Column::make('Slug', 'slug')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Descricao', 'descricao')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Codigo barras 1', 'codigo_barras_1')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Codigo barras 2', 'codigo_barras_2')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Codigo barras 3', 'codigo_barras_3')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Preço', 'preco_varejo_formatted', 'preco_varejo')
                ->sortable()
                ->searchable(),

            // Column::make('Preço atacado', 'preco_atacado')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Valor garçom', 'valor_garcom')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Preço Promocional', 'preco_promocao_formatted', 'preco_promocao')
                ->sortable()
                ->searchable()
                ->hidden(true, false),

            // Column::make('Promocao inicio', 'promocao_inicio_formatted', 'promocao_inicio')
            //     ->sortable(),

            // Column::make('Promocao fim', 'promocao_fim_formatted', 'promocao_fim')
            //     ->sortable(),

            // Column::make('Trib icms', 'trib_icms')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib csosn', 'trib_csosn')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib cst', 'trib_cst')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib origem produto', 'trib_origem_produto')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib cfop de', 'trib_cfop_de')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib cfop fe', 'trib_cfop_fe')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib ncm', 'trib_ncm')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Trib cest', 'trib_cest')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Estoque atual', 'estoque_atual')
                ->sortable()
                ->searchable(),

            // Column::make('Unidade medida', 'unidade_medida')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Codigo externo', 'codigo_externo')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Destaque', 'destaque')
            //     ->toggleable(),
            Column::make('Destaque?', 'destaque')
                    ->sortable('destaque'),

            // Column::make('Excluido em', 'deleted_at_formatted', 'deleted_at')
            //     ->sortable(),
            Column::make('Desativado?', 'is_active', 'deleted_at')
                ->sortable('deleted_at'),

            // Column::make('Views', 'views'),
            // Column::make('Somente mesa', 'somente_mesa')
            //     ->toggleable(),

            // Column::make('Ordem', 'ordem'),
            Column::make('Registrado em', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('categoria_titulo', 'categorias.id')
                ->dataSource(Categorias::withTenant()->get())
                ->optionValue('id')
                ->optionLabel('titulo'),
            Filter::inputText('titulo')->operators(['contains']),
            // Filter::inputText('slug')->operators(['contains']),
            // Filter::inputText('codigo_barras_1')->operators(['contains']),
            // Filter::inputText('codigo_barras_2')->operators(['contains']),
            // Filter::inputText('codigo_barras_3')->operators(['contains']),
            // Filter::datepicker('promocao_inicio'),
            // Filter::datepicker('promocao_fim'),
            // Filter::inputText('trib_icms')->operators(['contains']),
            // Filter::inputText('trib_csosn')->operators(['contains']),
            // Filter::inputText('trib_cst')->operators(['contains']),
            // Filter::inputText('trib_origem_produto')->operators(['contains']),
            // Filter::inputText('trib_cfop_de')->operators(['contains']),
            // Filter::inputText('trib_cfop_fe')->operators(['contains']),
            // Filter::inputText('trib_ncm')->operators(['contains']),
            // Filter::inputText('trib_cest')->operators(['contains']),
            Filter::inputText('unidade_medida')->operators(['contains']),
            // Filter::inputText('codigo_externo')->operators(['contains']),
            Filter::boolean('destaque')->label('Sim', 'Não'),
            // Filter::datetimepicker('deleted_at'),
            // Filter::boolean('somente_mesa'),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('restore')]
    public function restore($rowId, $params=null): void
    {
        $produto = Produtos::withTrashed()->findOrFail($rowId);

        if($params == null) {
            $this->dialog()->confirm([
                'icon'        => 'trash',
                'title'       => 'Você tem certeza?',
                'description' => 'Restaurar este produto?',
                'acceptLabel' => 'Sim, restaure',
                'method'      => 'restore',
                'params'      => [$rowId, 'Restored'],
            ]);
            return;
        }

        try {
            if(!$produto->trashed()) {
                $this->notification([
                    'title'       => 'Falha ao restaurar!',
                    'description' => 'Este Produto já esta ativo.',
                    'icon'        => 'error'
                ]);
            }else{
                $produto->restore();

                $this->notification([
                    'title'       => 'Produto restaurado!',
                    'description' => 'Produto foi restaurado com sucesso',
                    'icon'        => 'success'
                ]);
            }

            $this->dispatch('pg:eventRefresh-default');
        } catch (\Throwable $th) {
            //throw $th;
    
            $this->notification([
                'title'       => 'Falha ao restaurar!',
                'description' => 'Não foi possivel restaura o Produto.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(Produtos $row): array
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
