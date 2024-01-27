<div>
    <x-modal.card title="Convidar Usuário" blur wire:model.defer="usuarioInviteModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1 sm:col-span-2">
                <x-input label="E-mail" placeholder="example@mail.com" wire:model.debounce.500ms="email" />
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Enviar Convite" wire:click="send" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
