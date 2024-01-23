<?php

namespace App\Livewire\Tenant\Estoque;

use WireUi\Traits\Actions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\EstoqueMovimentacoes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
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

final class EstoqueTable extends PowerGridComponent
{
    // use WithExport;
    use Actions;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return EstoqueMovimentacoes::query()
        ->whereHas('produtos.empresas', fn($q) => $q->whereId(auth()->user()->empresas_id))
        ->with('produtos')
        ->leftJoin('produtos','produtos.id','estoque_movimentacoes.produtos_id')
        ->select('estoque_movimentacoes.*', 'produtos.titulo as produtos_titulo')
        ->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('produtos_titulo', function (EstoqueMovimentacoes $model) {
                return '<a wire:navigate href="'. route('tenant.produtos.index', ['search' => e($model->produtos_titulo)]) . '">'. e($model->produtos_titulo) .'</a>'; 
            })
            ->addColumn('tipo')
            ->addColumn('quantidade')
            // ->addColumn('motivo')
            // ->addColumn('fornecedores_id')
            // ->addColumn('nota_fiscal')
            ->addColumn('created_at_formatted', fn (EstoqueMovimentacoes $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Produto', 'produtos_titulo', 'produtos_id'),
            Column::make('Tipo', 'tipo')
                ->sortable()
                ->searchable(),

            Column::make('Quantidade', 'quantidade')
                ->sortable()
                ->searchable(),

            // Column::make('Motivo', 'motivo')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Fornecedores id', 'fornecedores_id'),
            // Column::make('Nota fiscal', 'nota_fiscal')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Registrado em', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('tipo')->operators(['contains']),
            // Filter::inputText('motivo')->operators(['contains']),
            // Filter::inputText('nota_fiscal')->operators(['contains']),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, $params=null): void
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja Remover esta Movimentação?<br/><strong>Não terá como reverter a remoção!</strong>',
                'acceptLabel' => 'Sim, remova',
                'method'      => 'delete',
                'params'      => [$rowId, 'Deleted'],
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $movimentacao = EstoqueMovimentacoes::with(['produtos' => fn($q) => $q->withTrashed()])->findOrFail($rowId);
            
            if(in_array($movimentacao->tipo, ['entrada', 'baixa'])) {
                $movimentacao->delete();

                if($movimentacao->tipo == 'entrada') {
                    if($movimentacao->produtos) $movimentacao->produtos->update(['estoque_atual' => $movimentacao->produtos->estoque_atual - $movimentacao->quantidade]);

                    $this->notification([
                        'title'       => 'Entrada excluída!',
                        'description' => 'Entrada no Estoque foi removida com sucesso.',
                        'icon'        => 'success'
                    ]);
                }else{
                    if($movimentacao->produtos) $movimentacao->produtos->update(['estoque_atual' => $movimentacao->produtos->estoque_atual + $movimentacao->quantidade]);

                    $this->notification([
                        'title'       => 'Baixa excluída!',
                        'description' => 'Baixa no Estoque foi removida com sucesso.',
                        'icon'        => 'success'
                    ]);
                }
                
                DB::commit();
            }
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
    
            $this->notification([
                'title'       => 'Falha na exclusão!',
                'description' => 'Não foi possivel remover a Movimentação.',
                'icon'        => 'error'
            ]);
        }
    }

    public function actions(EstoqueMovimentacoes $row): array
    {
        return [
            Button::add('delete')
                ->slot('Cancelar')
                ->bladeComponent('button', ['icon' => 'trash'])
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->id()
                ->dispatch('delete', ['rowId' => $row->id])
                ->can(in_array($row->tipo, ['entrada', 'baixa'])),
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
