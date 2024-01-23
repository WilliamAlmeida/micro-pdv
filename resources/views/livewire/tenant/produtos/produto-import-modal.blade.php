<div>
    <x-modal.card title="Importação de Produto" wire:model.defer="produtoImportModal"
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

            @if($dados)
                <div class="max-h-[300px] overflow-y-auto soft-scrollbar">
                    <table class="table-auto text-xs sm:text-base">
                        <thead>
                            <tr class="dark:text-white border-b-2 dark:border-indigo-500">
                                <th class="font-bold text-start pl-1">Categorias</th>
                                <th class="text-end">Nova?</th>
                            </tr>
                        </thead>
                        <tbody class="dark:text-white">
                            @if(count($categorias))
                                @foreach($categorias as $key => $item)
                                <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $key }}">
                                    <td>
                                        {{ $item['titulo'] }}
                                        <span class="block text-sm">{{ $item['slug'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($item['id'])
                                            <x-icon name="x" class="w-5 h-5 text-red-500 inline-block" />
                                        @else
                                            <x-icon name="check" class="w-5 h-5 text-lime-500 inline-block" />
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="border-b-2 border-gray-800">
                                    <td class="text-center text-[#6b6a6a]">Sem categorias.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="max-h-[500px] overflow-auto soft-scrollbar">
                    <table class="table-auto text-xs sm:text-base">
                        <thead>
                            <tr class="dark:text-white border-b-2 dark:border-indigo-500">
                                <th class="font-bold text-start pl-1">Produtos</th>
                            </tr>
                        </thead>
                        <tbody class="dark:text-white">
                            @if(count($produtos))
                                @foreach($produtos as $key => $item)
                                <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $key }}">
                                    <td>
                                        @json($item)
                                    </td>
                                    {{-- <td class="text-end">
                                        @if($item['id'])
                                            <x-icon name="x" class="w-5 h-5 text-red-500 inline-block" />
                                        @else
                                            <x-icon name="check" class="w-5 h-5 text-lime-500 inline-block" />
                                        @endif
                                    </td> --}}
                                </tr>
                                @endforeach
                            @else
                                <tr class="border-b-2 border-gray-800">
                                    <td class="text-center text-[#6b6a6a]">Sem produtos.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <x-button primary label="Baixar Modelo" href="{{ asset('assets/modelos-importacao/produtos.xlsx') }}" download />
                <div class="flex gap-x-4">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Importar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>