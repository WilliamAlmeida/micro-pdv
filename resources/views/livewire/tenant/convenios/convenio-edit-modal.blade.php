<div>
    <x-modal.card title="Edição de Convênio" blur wire:model.defer="convenioEditModal" max-width="3xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($form->cnpj)
                <x-input label="Inscrição Estadual" placeholder="Informe a Inscrição Estadual" wire:model="form.inscricao_estadual" />
            @endif
            
            <div class="col-span-1 sm:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-inputs.maskable label="CPF" mask="###.###.###-##" placeholder="Informe o CPF" wire:model.live.debounce.500ms="form.cpf" emitFormatted="true" :disabled="!empty($form->cnpj)" />
                    <x-inputs.maskable label="CNPJ" mask="##.###.###/####-##" placeholder="Informe o CNPJ" wire:model.blur="form.cnpj" emitFormatted="true" :disabled="!empty($form->cpf)"
                        wire:keydown.enter="pesquisar_cnpj" wire:loading.attr="disabled">
                        <x-slot name="append">
                            <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                                <x-button class="h-full rounded-r-md" icon="search" primary flat squared wire:loading.attr="disabled" wire:click="pesquisar_cnpj" :disabled="!empty($form->cpf)" />
                            </div>
                        </x-slot>
                    </x-inputs.maskable>
                </div>
            </div>
            
            <x-input wire:model.blur="form.nome_fantasia" label="Nome Fantasia" placeholder="Nome Fantasia" hint="{{ $form->slug }}" />
            <x-input wire:model="form.razao_social" label="Razão Social" placeholder="Razão Social" />

            <div class="col-span-1 sm:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <x-input label="CEP" placeholder="00.000-000" wire:model="form.end_cep" emitFormatted="true">
                        <x-slot name="append">
                            <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                                <x-button class="h-full rounded-r-md" icon="search" primary flat squared wire:click="pesquisar_cep" />
                            </div>
                        </x-slot>
                    </x-input>

                    <x-select label="Estado" placeholder="Selecione um Estado" :options="$array_estados" option-label="uf" option-value="id" wire:model.defer="form.idestado" />
                    
                    <div class="col-span-2"><x-input label="Município" placeholder="Informe a Município" wire:model="form.end_cidade" /></div>
                </div>
            </div>
                
            <div class="col-span-1 sm:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div class="col-span-2"><x-input label="Bairro" placeholder="Informe o Bairro" wire:model="form.end_bairro" /></div>
                    <div class="col-span-2"><x-input label="Logradouro" placeholder="Informe o Logradouro" wire:model="form.end_logradouro" /></div>
                    <x-input label="Número" placeholder="Informe o Número" wire:model="form.end_numero" />
                </div>
            </div>
                
            <x-input label="Complemento" placeholder="Informe um Complemento" wire:model="form.end_complemento" />
        </div>
            
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                @if(!$convenio?->trashed())
                    <x-button flat negative label="Desativar" wire:click="delete" />
                @else
                    <x-button flat positive label="Restaurar" wire:click="restore" />
                    <x-button flat negative label="Deletar" wire:click="delete" />
                @endif

                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Salvar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>