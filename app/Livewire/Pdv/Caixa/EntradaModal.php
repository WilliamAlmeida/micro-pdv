<?php

namespace App\Livewire\Pdv\Caixa;

use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use App\Traits\HelperActions;

use App\Traits\Pdv\CaixaActions;

use App\Traits\Pdv\CaixaTickets;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\Pdv\Caixa\EntradaForm;

class EntradaModal extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    public $caixa;

    public $depositCashModal = false;
    public EntradaForm $entradaForm;

    #[On('realizar_entrada')]
    public function realizar_entrada()
    {
        $this->entradaForm->reset();
        $this->entradaForm->resetValidation();

        $this->js('$openModal("depositCashModal")');

        $this->set_focus('entrada_valor');
    }

    public function salvar_entrada()
    {
        $this->entradaForm->validate();

        $this->caixa = $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        DB::beginTransaction();

        try {
            $resultado = $this->caixa->entradas()->create([
                'tipo'      => 'e',
                'motivo'    => $this->entradaForm->motivo,
                'valor'     => $this->entradaForm->valor
            ]);
    
            if($resultado) {
                $this->caixa->update(['entrada_total' => $this->caixa->entradas()->sum('valor')]);
                $result = $this->printEntrada($resultado->id);
                throw_if(array_key_exists('error', $result), $result['message']);
            }
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Entrada finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->reset('depositCashModal');

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Não foi possivel realizar a Entrada.',
                'icon'        => 'error'
            ]);
        }
    }

    #[On('onCloseDepositCashModal')]
    public function onCloseDepositCashModal()
    {
        $this->set_focus('pesquisar_produto');
    }

    public function render()
    {
        return view('livewire.pdv.caixa.entrada-modal');
    }
}
