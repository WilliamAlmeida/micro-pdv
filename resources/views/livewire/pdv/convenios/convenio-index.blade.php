<div>
    <div class="flex flex-col sm:flex-row h-screen" id="pdv">
        <div class="flex-none sm:w-1/4 bg-gray-300 dark:bg-gray-800 overflow-y-auto soft-scrollbar">
            {{-- @include('livewire.pdv.convenios.includes.actionSidebar') --}}

            <div class="flex flex-col gap-2 p-2 h-full">
                {{-- <div class="text-center font-bold dark:text-white">Ações</div> --}}

                <div class="flex flex-wrap justify-center sm:justify-normal sm:flex-nowrap sm:flex-col gap-y-2 gap-x-1 flex-grow"
                    x-data="{vendas_ate: @entangle('vendas_ate')}"
                    x-init="$watch('vendas_ate', value => (value == null) ? $wire.pesquisar_cliente() : null)"
                    >
                    <x-datetime-picker
                    label="Vales até"
                    placeholder="Vendas até"
                    parse-format="DD-MM-YYYY"
                    wire:model.defer="vendas_ate"
                    without-time="true"
                    />
            
                    <div x-show="vendas_ate">
                        <x-select
                        label="Cliente"
                        wire:model.defer="cliente_id"
                        placeholder="Pesquise pelo nome"
                        :async-data="route('api.clientes')"
                        option-label="nome_fantasia"
                        option-value="id"
                        x-on:selected="$wire.pesquisar_cliente()"
                        x-on:clear="$wire.pesquisar_cliente()"
                        id="cliente_id"
                        class="col-span-2"
                        />
                    </div>

                    @if($cliente_selecionado)
                        <x-button primary label="Filtrar" wire:click="filtrar_itens" />
                    @else
                        <x-button secondary label="Filtrar" disabled />
                    @endif
                </div>
            
                <div class="sm:mt-auto hidden sm:flex sm:flex-col gap-2 pb-2 sm:pb-0">
                    @if($cliente_selecionado)
                        <x-button href="{{ route('pdv.convenios') }}" label="Voltar" wire:navigate primary class="w-full" />
                    @else
                        <x-button href="{{ route('pdv.index') }}" label="Voltar para Caixa" wire:navigate negative class="w-full" />
                    @endif

                    <div class="flex gap-x-3 self-center">
                        <x-icon name="sun" class="w-5 h-5 dark:text-white" />
                        <div x-data="{ checked: localStorage.theme === 'dark' }">
                            <x-toggle lg x-on:click="theme = (theme == 'dark' ? 'light' : 'dark')" x-bind:checked="checked" />
                        </div>
                        <x-icon name="moon" class="w-5 h-5 dark:text-white" />
                    </div>
                </div>
            </div>
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
                    @include('livewire.pdv.convenios.includes.infoSaleGrid')
                </div>

                <div class="flex-grow overflow-y-auto soft-scrollbar">
                    @include('livewire.pdv.convenios.includes.gridProductTable')
                </div>

                <div class="flex-none self-stretch bg-gray-300 dark:bg-gray-800">
                    @include('livewire.pdv.convenios.includes.infoRecordGrid')
                </div>
            </div>
        </div>
    </div>

    @include('livewire.pdv.convenios.includes.returnProductModal')
    @include('livewire.pdv.convenios.includes.divideProductModal')
</div>
