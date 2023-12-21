<div>
    <div class="flex flex-col sm:flex-row h-screen" id="pdv">
        <div class="flex-none sm:w-1/6 bg-gray-300 dark:bg-gray-800 overflow-y-auto soft-scrollbar">
            @include('livewire.pdv.caixa.includes.sidebar')
        </div>
        
        <div class="flex flex-col flex-grow overflow-y-auto soft-scrollbar">
            <div class="flex flex-col h-full">
                <div class="flex-none bg-black">
                    @include('livewire.pdv.caixa.includes.infoUserGrid')
                </div>
                
                <div class="flex-grow overflow-y-auto soft-scrollbar">
                    @include('livewire.pdv.caixa.includes.listProductTable')
                </div>
                
                <div class="flex-none self-stretch bg-gray-300 dark:bg-gray-800
                    {{-- fixed sm:relative bg-black sm:bg-transparent top-0 left-0 w-screen sm:w-auto --}}
                    ">
                    @include('livewire.pdv.caixa.includes.searchProductBar')
                </div>
            </div>
        </div>
    </div>

    @include('livewire.pdv.caixa.includes.searchProductModal')
    @include('livewire.pdv.caixa.includes.editProductModal')
</div>
