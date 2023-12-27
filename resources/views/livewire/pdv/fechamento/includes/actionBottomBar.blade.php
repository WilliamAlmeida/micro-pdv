<div class="flex flex-row justify-center gap-2 sm:gap-40 p-2 border-t-2 dark:border-t-gray-700">
    <div>
        <x-button negative label="Voltar para Caixa" href="{{ route('pdv.index') }}" wire:navigate class="min-w-[140px]" />
    </div>
    <div>
        <x-button primary label="Fechar Caixa" class="min-w-[140px]" wire:click="fechar_caixa" />
    </div>
</div>