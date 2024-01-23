<?php

namespace App\Livewire\Tenant\Pdv\Caixa;

use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use App\Traits\HelperActions;

use App\Traits\Pdv\CaixaActions;

use App\Traits\Pdv\CaixaTickets;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\Tenant\Pdv\Caixa\SangriaForm;

class SangriaModal extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    public $caixa;

    public $withdrawalCashModal = false;
    public SangriaForm $sangriaForm;

    #[On('realizar_sangria')]
    public function realizar_sangria()
    {
        $this->sangriaForm->reset();
        $this->sangriaForm->resetValidation();

        $this->js('$openModal("withdrawalCashModal")');

        $this->set_focus('sangria_valor');
    }

    public function salvar_sangria()
    {
        $this->sangriaForm->validate();

        $this->caixa = $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        if($this->sangriaForm->valor > $this->caixa->vendas_encerradas()->sum('valor_total') + $this->caixa->entrada_total) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Valor da Sangria superior ao Valor do Caixa.',
                'icon'        => 'info'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $resultado = $this->caixa->sangrias()->create([
                'tipo'      => 's',
                'motivo'    => $this->sangriaForm->motivo,
                'valor'     => $this->sangriaForm->valor
            ]);
    
            if($resultado) {
                $this->caixa->update(['sangria_total' => $this->caixa->sangrias()->sum('valor')]);
                $result = $this->printSangria($resultado->id);
                throw_if(array_key_exists('error', $result), $result['message']);
            }
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Sangria finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->reset('withdrawalCashModal');

            $this->dispatch('refreshCaixa');

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Não foi possivel realizar a Sangria.',
                'icon'        => 'error'
            ]);
        }
    }

    #[On('onCloseWithdrawalCashModal')]
    public function onCloseWithdrawalCashModal()
    {
        $this->set_focus('pesquisar_produto');
    }

    public function render()
    {
        return view('livewire.tenant.pdv.caixa.sangria-modal');
    }
}
