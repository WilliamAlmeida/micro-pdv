<div class="flex flex-row justify-center gap-2 sm:gap-40 p-2 border-t-2 dark:border-t-gray-700">
    @if(!$fechamento_obrigatorio)
        <div>
            <x-button negative label="Voltar para Caixa" href="{{ route('tenant.pdv.index', tenant()) }}" wire:navigate class="min-w-[140px]" />
        </div>
    @endif
    <div>
        <x-button primary label="Fechar Caixa" class="min-w-[140px]" wire:click="fechar_caixa" />
    </div>
</div>