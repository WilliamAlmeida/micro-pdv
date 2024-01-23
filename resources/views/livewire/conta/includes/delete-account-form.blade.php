<div class="flex flex-col gap-4">
    <h2 class="font-bold text-lg">Deletar Conta</h2>
    <p>Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir sua conta, baixe todos os dados ou informações que deseja reter.</p>

    <x-button negative label="Deletar Conta" x-on:click="$openModal('deleteAccountModal')" />
</div>

<x-modal wire:model.defer="deleteAccountModal">
    <x-card title="Você tem certeza que quer deletar essa conta?">
        <p class="text-gray-600">
            Uma vez que sua conta é deletada, todos os dados serão permanentemente deletados. Por favor, entre com a sua senha para confirmar que você quer deletar permanentemente sua conta.
        </p>

        <div class="mt-3">
            <x-inputs.password label="Senha" wire:model="password_to_delete" id="current_password_2" />
        </div>
 
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button negative label="Deletar Conta" wire:click="deletar_conta" />
            </div>
        </x-slot>
    </x-card>
</x-modal>