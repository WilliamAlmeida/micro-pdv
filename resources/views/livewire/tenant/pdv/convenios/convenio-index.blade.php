<div>
    <div class="flex flex-col sm:flex-row h-screen" id="pdv">
        <div class="flex-none sm:w-1/6 bg-gray-300 dark:bg-gray-800 overflow-y-auto soft-scrollbar">
            @include('livewire.tenant.pdv.convenios.includes.actionSidebar')
        </div>

        <div class="flex flex-col flex-grow overflow-y-auto soft-scrollbar">
            <div class="flex flex-col h-full" x-data="{
                valor_total_selecionado: 0,
                itens_selecionados: @entangle('itens_selecionados'),
                calcula_selecionados() {
                    this.valor_total_selecionado = 0;

                    let rows = document.querySelectorAll('tbody tr input[type=checkbox]:checked');

                    rows.forEach((e) => {
                        this.valor_total_selecionado += parseFloat(e.getAttribute('data-valor_total'));
                    });
                },
                init() {
                    $watch('itens_selecionados', value => this.calcula_selecionados());
                    $nextTick(() => this.calcula_selecionados());
                }
            }">
                <div class="flex-none bg-black text-white">
                    @includeWhen($vendas_status == 0, 'livewire.tenant.pdv.convenios.includes.opened.infoSaleGrid')
                    @includeWhen($vendas_status == 1 || $vendas_status == 3, 'livewire.tenant.pdv.convenios.includes.payed.infoSaleGrid')
                    @includeWhen($vendas_status == 2, 'livewire.tenant.pdv.convenios.includes.returned.infoSaleGrid')
                </div>

                <div class="flex-grow overflow-y-auto soft-scrollbar">
                    @includeWhen($vendas_status == 0, 'livewire.tenant.pdv.convenios.includes.opened.gridProductTable')
                    @includeWhen($vendas_status == 1, 'livewire.tenant.pdv.convenios.includes.payed.gridProductTable')
                    @includeWhen($vendas_status == 2, 'livewire.tenant.pdv.convenios.includes.returned.gridProductTable')
                    @includeWhen($vendas_status == 3, 'livewire.tenant.pdv.convenios.includes.payed.gridReceivementTable')
                </div>

                <div class="flex-none self-stretch bg-gray-300 dark:bg-gray-800">
                    @includeWhen($vendas_status == 0, 'livewire.tenant.pdv.convenios.includes.opened.infoRecordGrid')
                </div>
            </div>
        </div>
    </div>

    @if($vendas_status == 0)
        @include('livewire.tenant.pdv.convenios.includes.opened.returnProductModal')
        @include('livewire.tenant.pdv.convenios.includes.opened.divideProductModal')
        @include('livewire.tenant.pdv.convenios.includes.opened.receivementModal')
    @endif
</div>
