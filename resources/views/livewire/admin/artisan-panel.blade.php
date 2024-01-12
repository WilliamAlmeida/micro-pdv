<div>
    <x-modal wire:model.defer="artisanPanelModal" align="end"
    >
        <x-card class="bg-secondary-400 dark:bg-secondary-800 !rounded-none">
            <form wire:submit="runCommand">
                <div class="flex flex-col gap-2">
                    <p class="text-lg text-center text-white">Artisan Panel</p>

                    <label class="block text-sm font-medium text-white" for="artisan_command">Command</label>
                    <x-input id="artisan_command" placeholder="migrate:status" wire:model="command" />
                    <label class="block text-sm font-medium text-white" for="artisan_parameters">Parameters</label>
                    <x-input id="artisan_parameters" placeholder="Separe each parameter with ;" wire:model="parameters" />
                </div>

                @if($output)
                    <p class="bg-secondary-100 dark:bg-black dark:text-lime-400 mt-3 p-2 border border-secondary-300 dark:border-gray-600 rounded-md text-xs">
                        {!! $output !!}
                    </p>
                @endif
        
                {{-- <x-slot name="footer"> --}}
                    <div class="flex justify-end gap-4 mt-3">
                        <x-button flat label="Close" x-on:click="close" />
                        <x-button primary label="Run" wire:click="runCommand" type="submit" />
                    </div>
                {{-- </x-slot> --}}
            </form>
        </x-card>
    </x-modal>
</div>
