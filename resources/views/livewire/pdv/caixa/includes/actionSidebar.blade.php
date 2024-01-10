<div class="flex flex-col gap-2 p-2 h-full">
    <div class="text-center font-bold dark:text-white">Ações</div>
    
    <div class="flex flex-wrap justify-center sm:justify-normal sm:flex-nowrap sm:flex-col gap-y-2 gap-x-1 flex-grow">
        @if(count($caixa->vendas) && $caixa->vendas->firstWhere('status', 0))
            <x-button primary label="Encerrar" wire:click="encerrar_venda" />
        @else
            <x-button secondary label="Encerrar" disabled wire:click="encerrar_venda" />
        @endif
        {{-- <x-button warning label="Orçamento" disabled="true" /> --}}
        <x-button negative label="Sangria" wire:click="realizar_sangria" />
        <x-button positive label="Entrada" wire:click="realizar_entrada" />
        @if(count($caixa->vendas) && $caixa->vendas->firstWhere('status', 1))
            <x-button sky label="Registros" href="{{ route('pdv.vendas') }}" wire:navigate />
        @else
            <x-button secondary label="Registros" disabled />
        @endif
        @if(count($caixa->vendas) && $caixa->vendas->firstWhere('status', 1))
            <x-button purple label="Fechar Caixa" href="{{ route('pdv.fechamento') }}" wire:navigate />
        @else
            <x-button secondary label="Fechar Caixa" disabled />
        @endif
        <x-button pink label="Convênios" href="{{ route('pdv.convenios') }}" wire:navigate />
        @if(count($caixa->vendas) && $caixa->vendas->firstWhere('status', 1))
            <x-button rose label="Reimpressão" wire:click="imprimir_ultima_venda" />
        @else
            <x-button secondary label="Reimpressão" disabled wire:click="imprimir_ultima_venda" />
        @endif
        <x-button black label="Sair do Caixa" wire:click="sair_caixa" class="flex sm:hidden" />
    </div>

    <div class="sm:mt-auto hidden sm:flex sm:flex-col gap-2 pb-2 sm:pb-0">
        <x-button black label="Sair do Caixa" wire:click="sair_caixa" class="w-full" />
        <div class="flex gap-x-3 self-center">
            <x-icon name="sun" class="w-5 h-5 dark:text-white" />
            <div x-data="{ checked: localStorage.theme === 'dark' }">
                <x-toggle lg x-on:click="theme = (theme == 'dark' ? 'light' : 'dark')" x-bind:checked="checked" />
            </div>
            <x-icon name="moon" class="w-5 h-5 dark:text-white" />
        </div>
    </div>
</div>