<div>
    <div class="flex flex-col sm:flex-row h-screen" id="pdv">
        <div class="flex-none sm:w-1/4 bg-gray-300 dark:bg-gray-800 overflow-y-auto soft-scrollbar">
            @include('livewire.pdv.vendas.includes.actionSidebar')
        </div>

        <div class="flex flex-col flex-grow overflow-y-auto soft-scrollbar">
            <div class="flex flex-col h-full">
                <div class="flex-none bg-black">
                    @include('livewire.pdv.vendas.includes.infoSaleGrid')
                </div>

                <div class="flex-grow overflow-y-auto soft-scrollbar">
                    @include('livewire.pdv.vendas.includes.gridProductTable')
                </div>

                <div class="flex-none self-stretch bg-gray-300 dark:bg-gray-800">
                    @include('livewire.pdv.vendas.includes.infoRecordGrid')
                </div>
            </div>
        </div>
    </div>
</div>
