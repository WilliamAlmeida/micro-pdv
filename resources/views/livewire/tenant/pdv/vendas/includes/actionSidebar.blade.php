<div class="flex flex-col space-y-2 h-full dark:text-white">
    <div class="flex justify-between px-1 border-b-2 border-b-black dark:border-gray-700">
        <strong>Operador</strong>
        <span>{{ Str::upper($caixa->user->name ?: 'Desconhecido') }}</span>
    </div>

    <div class="sm:text-center mx-2 sm:mx-0">
        <strong>Registros</strong>

        <div class="inline-block sm:hidden float-right">
            @if($venda_selecionada)
                <x-button href="{{ route('tenant.pdv.vendas', tenant()) }}" label="Voltar" wire:navigate primary sm class="w-full" />
            @else
                <x-button href="{{ route('tenant.pdv.index', tenant()) }}" label="Voltar para Caixa" wire:navigate negative sm class="w-full" />
            @endif
        </div>
    </div>

    <div class="flex flex-wrap justify-center sm:justify-normal sm:flex-nowrap sm:flex-col flex-grow overflow-x-auto">
        @include('livewire.tenant.pdv.vendas.includes.gridSaleTable')
    </div>

    <div class="sm:mt-auto hidden sm:flex p-2">
        @if($venda_selecionada)
            <x-button href="{{ route('tenant.pdv.vendas', tenant()) }}" label="Voltar" wire:navigate primary class="w-full" />
        @else
            <x-button href="{{ route('tenant.pdv.index', tenant()) }}" label="Voltar para Caixa" wire:navigate negative class="w-full" />
        @endif

        {{-- <div class="flex gap-x-3 self-center">
            <x-icon name="sun" class="w-5 h-5 dark:text-white" />
            <div x-data="{ checked: localStorage.theme === 'dark' }">
                <x-toggle lg x-on:click="theme = (theme == 'dark' ? 'light' : 'dark')" x-bind:checked="checked" />
            </div>
            <x-icon name="moon" class="w-5 h-5 dark:text-white" />
        </div> --}}

    </div>
</div>