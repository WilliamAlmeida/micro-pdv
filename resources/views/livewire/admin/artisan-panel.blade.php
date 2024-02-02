<div>
    <x-modal wire:model.defer="artisanPanelModal" align="end"
    >
        <x-card class="bg-secondary-400 dark:bg-secondary-800 !rounded-none">
            <form wire:submit="runCommand">
                <div class="flex flex-col gap-2">
                    <p class="text-lg text-center text-white">Artisan Panel</p>

                    <label class="block text-sm font-medium text-white" for="artisan_command">Command</label>
                    <div x-data="{open: false, command: @entangle('command'), suggestions: @entangle('suggestions')}"  @click.away="open = false">
                        <input
                        x-model="command"
                        x-on:focus="open = true"
                        class="placeholder-secondary-400 dark:bg-secondary-800 dark:text-secondary-400 dark:placeholder-secondary-500 border border-secondary-300 focus:ring-primary-500 focus:border-primary-500 dark:border-secondary-600 form-input block w-full sm:text-sm rounded-md transition ease-in-out duration-100 focus:outline-none shadow-sm"
                        placeholder="list; migrate:status;"
                        id="artisan_command"
                        />
                        <div class="absolute w-[95%] z-50 backdrop-blur-sm">
                            <ul x-show="open" class="border border-secondary-300 dark:border-gray-600 mt-2 top-0" x-cloak>
                                <template x-for="suggestion in suggestions" :key="suggestion">
                                    <li class="cursor-pointer bg-secondary-100/25 hover:bg-indigo-500/25 hover:text-indigo-500 px-2 py-1 transition-all"
                                    x-text="suggestion" x-on:click="command = suggestion; open = false"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- <label class="block text-sm font-medium text-white" for="artisan_parameters">Parameters</label>
                    <x-input id="artisan_parameters" placeholder="Separe each parameter with ;" wire:model="parameters" /> --}}
                </div>

                @if($output)
                    <p class="bg-secondary-100 dark:bg-black dark:text-lime-400 mt-3 p-2 border border-secondary-300 dark:border-gray-600 rounded-l-md rounded-r-sm text-xs overflow-auto max-h-96 scrollbar scrollbar-w-2 scroll-smooth">
                        {!! $output !!}
                    </p>
                @endif
        
                {{-- <x-slot name="footer"> --}}
                    <div class="flex justify-end gap-4 mt-3">
                        <x-button flat label="Close" x-on:click="close" wire:loading.remove />
                        <x-button primary label="Run" type="submit" spinner="runCommand" loading-delay="long" wire:loading.remove />
                    </div>
                {{-- </x-slot> --}}
            </form>
        </x-card>
    </x-modal>
</div>