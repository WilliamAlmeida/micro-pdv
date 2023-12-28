<div>
    <x-modal.card title="Importação de Produto" fullscreen wire:model.defer="produtoImportModal"
    x-on:close="$dispatch('onCloseProdutoImportModal')"
    >
        <div class="grid gap-4">
            @if(!$arquivo)
                <x-input label="Arquivo" placeholder="Arquivo" wire:model="arquivo" type="file" accept=".xls, .xlsx" />
            @endif

            <div class="spinner-border" role="status" wire:loading wire:target="arquivo"><span class="visually-hidden">Loading...</span></div>

            @if($arquivo)
                <div class="border p-2 rounded-md">
                    {{-- {{ $arquivo->temporaryUrl() }} --}}
                    Arquivo enviado.
                </div>
                <x-button nevative icon="x" label="Remover" wire:click="removeFile" />
            @endif

            <x-toggle left-label="Apagar os Produtos Anteriores" wire:model.defer="resetar_produtos" />
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Importar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>