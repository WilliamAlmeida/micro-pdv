<div class="flex flex-col gap-4">
    <h2 class="font-bold text-lg">Atualizar a senha</h2>
    <p>Certifique-se de que sua conta esteja usando uma senha longa e aleatória para permanecer segura.</p>

    <div x-data="{ show: @entangle('passwordForm') }">
        <form class="flex flex-col gap-4" wire:submit="atualizar_senha" x-show="show" x-transition>
            <x-inputs.password label="Senha Atual" wire:model="current_password" />
            <x-inputs.password label="Nova Senha" wire:model="password" />
            <x-inputs.password label="Confirmação da Senha" wire:model="password_confirmation" />

            <div class="flex justify-between">
                <x-button negative label="Cancelar" x-on:click="show = false; $wire.cancel_validation()" />
                <x-button primary label="Salvar" type="submit" />
            </div>
        </form>

        <x-button primary label="Trocar Senha" x-on:click="show = true" x-show="!show" />
    </div>
</div>