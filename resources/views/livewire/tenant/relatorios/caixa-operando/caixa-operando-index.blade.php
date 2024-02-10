<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Caixa Operando') }}
        </h2>
    </x-slot>

    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <x-select
                    placeholder="Selecione um Caixa"
                    :options="$caixas"
                    option-label="user.name"
                    option-value="id"
                    wire:model.defer="caixa_selecionado"
                    class="max-w-xs"
                    x-on:selected="$wire.select_caixa()"
                    x-on:clear="$wire.select_caixa()"
                    />

                @if($caixa_selecionado)
                    <livewire:tenant.relatorios.caixa-operando.caixa-operando-widget :caixa='$caixa_selecionado' />
                @endif
            </div>
        </div>
    </div>
</div>