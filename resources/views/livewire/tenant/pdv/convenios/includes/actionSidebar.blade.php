<div class="flex flex-col space-x-2 space-y-2 p-2 h-full">
    {{-- <div class="text-center font-bold dark:text-white">Ações</div> --}}

    <div class="flex flex-wrap sm:flex-nowrap sm:flex-col space-y-2 space-x-1 flex-grow"
        x-data="{vendas_ate: @entangle('vendas_ate')}"
        x-init="$watch('vendas_ate', value => (value == null) ? $wire.pesquisar_cliente() : null)"
        >

        <label class="w-full text-sm font-medium text-gray-700 dark:text-gray-400 px-1">
            Lançamentos
        </label>

        <div class="w-full grid grid-cols-2 gap-2">
            <x-radio id="itens_em_aberto" label="Não Pagos" value="0" wire:model.defer="vendas_status" />
            <x-radio id="itens_devolvidos" label="Devolvidos" value="2" wire:model.defer="vendas_status" />
            <x-radio id="recebimentos" label="Recebimentos" value="3" wire:model.defer="vendas_status" />
            <x-radio id="itens_pagas" label="Pagos (Itens)" value="1" wire:model.defer="vendas_status" />
        </div>

        <x-datetime-picker
        label="Vales até"
        placeholder="Vendas até"
        parse-format="DD-MM-YYYY"
        wire:model.defer="vendas_ate"
        without-time="true"
        />

        <div x-show="vendas_ate" class="w-full">
            <x-select
            label="Cliente"
            wire:model.defer="cliente_id"
            placeholder="Pesquise pelo nome"
            :async-data="route('api.clientes', ['empresa' => tenant() ?? null])"
            option-label="nome_fantasia"
            option-value="id"
            x-on:selected="$wire.pesquisar_cliente()"
            x-on:clear="$wire.pesquisar_cliente()"
            id="cliente_id"
            />
        </div>

        @if($cliente_selecionado)
            <x-button primary label="Filtrar" wire:click="filtrar_itens" />
        @else
            <x-button secondary label="Filtrar" disabled />
            <x-button href="{{ route('tenant.pdv.index', tenant()) }}" label="Voltar para Caixa" wire:navigate negative class="sm:hidden" />
        @endif
    </div>

    <div class="sm:mt-auto hidden sm:flex sm:flex-col gap-2 pb-2 sm:pb-0">
        @if($cliente_selecionado)
            <x-button href="{{ route('tenant.pdv.convenios', tenant()) }}" label="Voltar" wire:navigate primary class="w-full" />
        @else
            <x-button href="{{ route('tenant.pdv.index', tenant()) }}" label="Voltar para Caixa" wire:navigate negative class="w-full" />
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