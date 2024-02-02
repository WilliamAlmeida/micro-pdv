<div>
    <x-modal.card title="Edição do Usuário" blur wire:model.defer="usuarioEditModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Nome" placeholder="Nome" wire:model="name" id="edit_name" />

            <div class="col-span-1 sm:col-span-2">
                <x-input label="E-mail" placeholder="example@mail.com" wire:model.live.debouce.200ms="email" id="edit_email" />
            </div>
     
            <x-inputs.password type="password" label="Senha" placeholder="Senha" wire:model.blur="password" id="edit_password" />
            @if($password)
                <x-inputs.password label="Confirmação da Senha" placeholder="Senha" wire:model.blur="password_confirmation" id="edit_password_confirmation" />
            @endif
     
            {{-- <div class="col-span-1 sm:col-span-2 cursor-pointer bg-gray-100 rounded-xl shadow-md h-72 flex items-center justify-center">
                <div class="flex flex-col items-center justify-center">
                    <x-icon name="cloud-upload" class="w-16 h-16 text-blue-600" />
                    <p class="text-blue-600">Click or drop files here</p>
                </div>
            </div> --}}
        </div>
     
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <x-button flat negative label="Deletar" wire:click="delete" />
     
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Atualizar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
