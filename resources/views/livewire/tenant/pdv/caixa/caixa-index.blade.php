<div>
    <div class="flex flex-col sm:flex-row h-screen" id="pdv">
        <div class="flex-none sm:w-1/6 bg-gray-300 dark:bg-gray-800 overflow-y-auto soft-scrollbar">
            @include('livewire.tenant.pdv.caixa.includes.actionSidebar')
        </div>

        <div class="flex flex-col flex-grow overflow-y-auto soft-scrollbar">
            <div class="flex flex-col h-full">
                <div class="flex-none bg-black">
                    @include('livewire.tenant.pdv.caixa.includes.infoUserGrid')
                </div>

                <div class="flex-grow overflow-y-auto soft-scrollbar">
                    @include('livewire.tenant.pdv.caixa.includes.gridProductTable')
                </div>

                <div class="flex-none self-stretch bg-gray-300 dark:bg-gray-800">
                    @include('livewire.tenant.pdv.caixa.includes.searchProductBar')
                </div>
            </div>
        </div>
    </div>

    <livewire:tenant.pdv.caixa.pesquisar-item-modal />
    <livewire:tenant.pdv.caixa.alterar-item-modal />
    <livewire:tenant.pdv.caixa.sangria-modal />
    <livewire:tenant.pdv.caixa.entrada-modal />
    @include('livewire.tenant.pdv.caixa.includes.paymentModal')
</div>
