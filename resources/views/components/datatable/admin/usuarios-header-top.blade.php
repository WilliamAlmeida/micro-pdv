<div>
    <x-button white label="Restaurar Usuários"
     wire:click="$dispatch('bulkRestoreEvent')" x-show="window.pgBulkActions.count('default')" class="mb-3 pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700" />

    {{-- <button wire:loading.attr="disabled" wire:loading.class="!cursor-wait" type="button" class="pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700" wire:click="$dispatch('bulkRestoreEvent')" x-show="window.pgBulkActions.count('default')">
        Restaurar Selecionados (<div x-text="window.pgBulkActions.count('default')"></div>)
    </button> --}}
</div>