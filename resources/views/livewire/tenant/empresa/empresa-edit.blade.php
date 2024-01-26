<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Empresa') }}
        </h2>
    </x-slot>
    
    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div x-data="{ activeTab: 'dados' }">
                        @if($empresa || !$readMode && !$empresa)
                        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                            <ul class="flex flex-wrap justify-evenly -mb-px text-sm font-medium text-center" id="default-tab" role="tablist">
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="dados-tab" @click="activeTab = 'dados'" :aria-selected="activeTab === 'dados' ? 'true' : 'false'" :class="{ 'border-blue-500 dark:border-blue-700': activeTab === 'dados' }"><x-icon name="identification" class="w-5 h-5 inline-block" /> Dados</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="horarios-tab" @click="activeTab = 'horarios'" :aria-selected="activeTab === 'horarios' ? 'true' : 'false'" :class="{ 'border-blue-500 dark:border-blue-700': activeTab === 'horarios' }"><x-icon name="clock" class="w-5 h-5 inline-block" /> Horários</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="seo-tab" @click="activeTab = 'seo'" :aria-selected="activeTab === 'seo' ? 'true' : 'false'" :class="{ 'border-blue-500 dark:border-blue-700': activeTab === 'seo' }"><x-icon name="search" class="w-5 h-5 inline-block" /> SEO</button>
                                </li>
                            </ul>
                        </div>
                        @endif

                        <div id="default-tab-content">

                            @if($empresa || !$readMode && !$empresa)
                            <div x-show="activeTab === 'dados'" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dados" role="tabpanel" aria-labelledby="dados-tab">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @if($readMode)
                                        <x-input label="Tipo de Empresa" value="{{ $empresa ? ($empresa->getTipoEmpresa()['name'] ?? null) : null }}" disabled="true" />
                                    @endif
                                    <x-select label="Tipo de Empresa" placeholder="Selecione um Tipo" :options="$array_tipos_empresas" option-label="name" option-value="id" wire:model.defer="form.id_tipo_empresa" :class="$readMode ? 'hidden' : null" />

                                    @if($form->cnpj)
                                        <x-input label="Inscrição Estadual" placeholder="Informe a Inscrição Estadual" wire:model="form.inscricao_estadual" :disabled="$readMode" />
                                    @endif

                                    <div class="col-span-1 sm:col-span-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <x-inputs.maskable label="CPF" mask="###.###.###-##" placeholder="Informe o CPF" wire:model="form.cpf" emitFormatted="true" :disabled="$readMode" />
                                            <x-inputs.maskable label="CNPJ" mask="##.###.###/####-##" placeholder="Informe o CNPJ" wire:model.live.debounce.500ms="form.cnpj" emitFormatted="true" :disabled="$readMode" />
                                        </div>
                                    </div>

                                    <x-input wire:model.blur="form.nome_fantasia" label="Nome Fantasia" placeholder="Nome Fantasia" hint="{{ $form->slug }}" :disabled="$readMode" />
                                    <x-input wire:model="form.razao_social" label="Razão Social" placeholder="Razão Social" :disabled="$readMode" />

                                    <div class="col-span-1 sm:col-span-2">
                                        <div class="grid grid-cols-3 sm:grid-cols-3 gap-4">
                                            <x-input label="CEP" placeholder="00.000-000" wire:model="form.end_cep" emitFormatted="true" :disabled="$readMode">
                                                <x-slot name="append">
                                                    <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                                                        <x-button class="h-full rounded-r-md" icon="search" primary flat squared wire:click="pesquisar_cep" :disabled="$readMode" />
                                                    </div>
                                                </x-slot>
                                            </x-input>
            
                                            @if($readMode)
                                                <x-input label="Estado" value="{{ $empresa->estado->uf ?? null }}" disabled="true" />
                                            @endif
                                            <x-select label="Estado" placeholder="Selecione um Estado" :options="$array_estados" option-label="uf" option-value="id" wire:model.defer="form.idestado" :class="$readMode ? 'hidden' : null" />

                                            <x-input label="Município" placeholder="Informe a Município" wire:model="form.end_cidade" :disabled="$readMode" />
                                        </div>
                                    </div>

                                    <div class="col-span-1 sm:col-span-2">
                                        <div class="grid grid-cols-3 sm:grid-cols-3 gap-4">
                                            <x-input label="Bairro" placeholder="Informe o Bairro" wire:model="form.end_bairro" :disabled="$readMode" />
                                            <x-input label="Logradouro" placeholder="Informe o Logradouro" wire:model="form.end_logradouro" :disabled="$readMode" />
                                            <x-input label="Número" placeholder="Informe o Número" wire:model="form.end_numero" :disabled="$readMode" />
                                        </div>
                                    </div>

                                    <x-input label="Complemento" placeholder="Informe um Complemento" wire:model="form.end_complemento" :disabled="$readMode" />
                                </div>
                            </div>

                            <div x-show="activeTab === 'horarios'" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="horarios" role="tabpanel" aria-labelledby="horarios-tab">
                                @foreach($form->horarios as $dia_semana => $values)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 content-center space-y-2">
                                        <span class="capitalize">{{ $array_days_of_week[$dia_semana] ?? 'Desconhecido' }} </span>
                                        @if(!$readMode)
                                            <x-button icon="plus" positive label="Adicionar" wire:click="add_hours('{{ $dia_semana }}')" />
                                        @endif
                                    </div>
                                    @forelse($values as $key => $value)
                                        @if(empty($value)) @continue @endif

                                        <div class="grid grid-cols-2 gap-4 py-3 sm:grid-cols-3 content-center">
                                            <x-time-picker label="Início" placeholder="00:00" format="24" wire:model.live="form.horarios.{{$dia_semana}}.{{$key}}.inicio" :disabled="$readMode" />
                                            <x-time-picker label="Término" placeholder="00:00" format="24" wire:model.live="form.horarios.{{$dia_semana}}.{{$key}}.fim" :disabled="$readMode" />
                                            @if(!$readMode)
                                                <div class="grid grid-cols-3 gap-4 content-end">
                                                    <x-button icon="x" negative label="Removar" wire:click="remove_hours('{{ $dia_semana }}', '{{ $key }}')" />
                                                </div>
                                            @endif
                                        </div>
                                        @empty
                                            @if($readMode)
                                                <div class="grid grid-cols-2 gap-4 py-3 sm:grid-cols-3 content-center">
                                                    <x-input label="Início" value="00:00" disabled="true" />
                                                    <x-input label="Término" value="00:00" disabled="true" />
                                                </div>
                                            @endif
                                    @endforelse
                                @endforeach
                            </div>

                            <div x-show="activeTab === 'seo'" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                @if($readMode)
                                    <x-input value="{{ str_replace(',', ', ', $form->keywords) }}" label="Palavras Chave" hint="Digite as palavras chaves separando por , (Virgula)" disabled="true" />
                                @else
                                    <x-input wire:model="form.keywords" label="Palavras Chave" hint="Digite as palavras chaves separando por , (Virgula)" :disabled="$readMode" />
                                @endif
                                <div class="mt-3">
                                    <x-textarea wire:model="form.description" label="Descrição" placeholder="Descrição" :disabled="$readMode" />
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-center gap-x-3">
                                @if(!$readMode)
                                    <div class="mt-3">
                                        <x-button flat label="Cancelar" class="w-24" wire:click="cancel" />
                                        <x-button primary label="Salvar" class="w-24" wire:click='save' />
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <x-button primary :label="$empresa ? 'Editar' : 'Registrar'" class="w-24" wire:click="$set('readMode', false)" />
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>