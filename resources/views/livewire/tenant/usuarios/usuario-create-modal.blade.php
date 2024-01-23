<div>
    <x-modal.card title="Criação de Usuário" blur wire:model.defer="usuarioCreateModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Nome" placeholder="Nome" wire:model.live="name" />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="E-mail" placeholder="example@mail.com" wire:model.live="email" />
            </div>
     
            <x-inputs.password type="password" label="Senha" placeholder="Senha" wire:model.blur="password" />
            @if($password)
                <x-inputs.password label="Confirmação da Senha" placeholder="Senha" wire:model.blur="password_confirmation" />
            @endif
        </div>
     
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Salvar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
