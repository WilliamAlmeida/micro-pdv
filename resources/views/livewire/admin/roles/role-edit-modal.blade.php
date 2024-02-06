<div>
    <x-modal.card title="Edição de Funções" blur wire:model.defer="roleEditModal" max-width="3xl"
    x-on:open="$dispatch('loadf')">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data="{
            filtro: '',
            selecteds: @entangle('selected'),
            toggleSelectAll (action) {
                let tabs = $el.querySelectorAll('#default-tab-content [role=tabpanel]');
                let tab;
                tabs.forEach((e) => {
                    if(window.getComputedStyle(e).display !== 'none') tab = e.id;
                });

                $el.querySelectorAll('#default-tab-content #'+tab+' [type=checkbox]').forEach((e) => {
                    if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                        e.checked = action;
                        let index = this.selecteds.indexOf(parseInt(e.name));
                        if(action) { if(index === -1) this.selecteds.push(parseInt(e.name)) }else{ if(index !== -1) this.selecteds.splice(index, 1) }
                    }
                });
            },
        }">
            <div class="col-span-full">
                <x-input label="Função" placeholder="Digite o nome da Função" wire:model="name" id="role_edit" />
            </div>

            <div class="col-span-full flex justify-between gap-x-3">
                <x-button negative icon="minus-circle" x-on:click="toggleSelectAll(0)" />
                <div class="flex-grow">
                    <x-input placeholder="Filtro" x-model="filtro">
                        <x-slot name="append">
                            <div class="absolute inset-y-0 right-0 flex items-center p-0.5" x-show="filtro != ''">
                                <x-button class="h-full rounded-r-md" icon="x" primary flat squared x-on:click="filtro = ''" />
                            </div>
                        </x-slot>
                    </x-input>
                </div>
                <x-button primary icon="check-circle" x-on:click="toggleSelectAll(1)" />
            </div>

            <div class="col-span-full">
                <div x-data="{ activeTab: 'global' }">
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap justify-evenly -mb-px text-sm font-medium text-center" id="default-tab" role="tablist">
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="global-tab" @click="activeTab = 'global'" :aria-selected="activeTab === 'global' ? 'true' : 'false'" :class="{ 'border-blue-500 dark:border-blue-700': activeTab === 'global' }"><x-icon name="globe" class="w-5 h-5 inline-block" /> Permissões Globais</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="tenant-tab" @click="activeTab = 'tenant'" :aria-selected="activeTab === 'tenant' ? 'true' : 'false'" :class="{ 'border-blue-500 dark:border-blue-700': activeTab === 'tenant' }"><x-icon name="home" class="w-5 h-5 inline-block" /> Permissões de Tenants</button>
                            </li>
                        </ul>
                    </div>
    
                    <div id="default-tab-content">
    
                        <div x-show="activeTab === 'global'" class="" id="global" role="tabpanel" aria-labelledby="global-tab">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @if(!empty($permissions))
                                    @foreach ($permissions as $group => $values)
                                        @if(Str::startsWith('tenant', $group)) @continue @endif
                                        <div class="select-none" x-data="{
                                            toggleSelectAll (action) {
                                                $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                                                    if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                                                        e.checked = action;
                                                        let index = selecteds.indexOf(parseInt(e.name));
                                                        if(action) { if(index === -1) selecteds.push(parseInt(e.name)) }else{ if(index !== -1) selecteds.splice(index, 1) }
                                                    }
                                                });
                                            },
                                            toggleSelect (option) {
                                                let index = selecteds.indexOf(option);
                                                if(index !== -1) { selecteds.splice(index, 1) }else{ selecteds.push(option) }
                                            },
                                            loadData () {
                                                let values = [...this.selecteds];
                                                this.toggleSelectAll(0);
                                
                                                $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                                                    if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                                                        let value = parseInt(e.name);
                                                        if(values.includes(value)) { e.checked = true; this.toggleSelect(value) }
                                                    }
                                                });
                                            }
                                        }" @loadf.window="loadData">
                                            <div class="flex justify-between">
                                                <span class="font-bold capitalize">{{ $group }}</span>
                                                <div>
                                                    {{-- <x-button xs primary flat label="Salvar" wire:dirty /> --}}
                                                    <x-button xs negative flat icon="minus-circle" x-on:click="toggleSelectAll(0)" />
                                                    <x-button xs positive flat icon="check-circle" x-on:click="toggleSelectAll(1)" />
                                                </div>
                                            </div>

                                            <ul class="[&>*]:border-b [&>*:last-child]:border-b-0">
                                                @foreach ($values as $key => $permission)
                                                    <li x-show="filtro === '' || '{{ $permission['name'] }}'.toLowerCase().includes(filtro.toLowerCase())">
                                                        <x-checkbox id="{{ $permission['id'] }}" label="{{ $permission['name'] }}" name="{{ $permission['id'] }}" x-on:click="toggleSelect({{ $permission['id'] }})" />
                                                    </li>
                                                @endforeach
                                            </ul>

                                            {{-- <div class="flex justify-end pt-1"><x-button xs primary wire:dirty label="Salvar" /></div> --}}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
    
                        <div x-show="activeTab === 'tenant'" class="" id="tenant" role="tabpanel" aria-labelledby="tenant-tab">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @if(!empty($permissions))
                                    @foreach ($permissions['tenant'] as $group => $values)
                                        <div class="select-none" x-data="{
                                            toggleSelectAll (action) {
                                                $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                                                    e.checked = action;
                                                    let index = selecteds.indexOf(parseInt(e.name));
                                                    if(action) { if(index === -1) selecteds.push(parseInt(e.name)) }else{ if(index !== -1) selecteds.splice(index, 1) }
                                                });
                                            },
                                            toggleSelect (option) {
                                                let index = selecteds.indexOf(option);
                                                if(index !== -1) { selecteds.splice(index, 1) }else{ selecteds.push(option) }
                                            },
                                            loadData () {
                                                let values = [...this.selecteds];
                                                this.toggleSelectAll(0);
                                
                                                $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                                                    if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                                                        let value = parseInt(e.name);
                                                        if(values.includes(value)) { e.checked = true; this.toggleSelect(value) }
                                                    }
                                                });
                                            }
                                        }" @loadf.window="loadData">
                                            <div class="flex justify-between">
                                                <span class="font-bold capitalize">{{ $group }}</span>
                                                <div>
                                                    {{-- <x-button xs primary flat label="Salvar" wire:dirty /> --}}
                                                    <x-button xs negative flat icon="minus-circle" x-on:click="toggleSelectAll(0)" />
                                                    <x-button xs positive flat icon="check-circle" x-on:click="toggleSelectAll(1)" />
                                                </div>
                                            </div>

                                            <ul class="[&>*]:border-b [&>*:last-child]:border-b-0">
                                                @foreach ($values as $key => $permission)
                                                    <li class="hover:border-r-8 border-r-indigo-500 pl-1 transition-all ease-in duration-200" x-show="filtro === '' || '{{ $permission['name'] }}'.toLowerCase().includes(filtro.toLowerCase())">
                                                        <x-checkbox id="{{ $permission['id'] }}" label="{{ $permission['name'] }}" name="{{ $permission['id'] }}" x-on:click="toggleSelect({{ $permission['id'] }})" />
                                                    </li>
                                                @endforeach
                                            </ul>

                                            {{-- <div class="flex justify-end pt-1"><x-button xs primary wire:dirty label="Salvar" /></div> --}}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
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
