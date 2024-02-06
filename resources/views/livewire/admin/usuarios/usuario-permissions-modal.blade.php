<div>
    <x-modal.card title="Permissões do Usuário" blur wire:model.defer="usuarioPermissionModal"
    x-on:open="$dispatch('loadf'); console.log(0)"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <x-native-select
                label="Tipo"
                :options="[
                ['name' => 'Usuário',  'id' => 0],
                ['name' => 'Empresa', 'id' => 1],
                ['name' => 'Admin', 'id' => 2],
                ]"
                option-label="name"
                option-value="id"
                value="{{ $user->type ?? '' }}"
                disabled
                />

                <x-input label="Nome" value="{{ $user->name ?? '' }}" disabled />

                <x-input label="E-mail" value="{{ $user->email ?? '' }}" disabled />
            </div>
     
            <div>
                <div class="select-none" x-data="{
                    selecteds: @entangle('selecteds'),
                    toggleSelectAll (action) {
                        $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                            if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                                e.checked = action;
                                let index = this.selecteds.indexOf(parseInt(e.id));
                                if(action) { if(index === -1) this.selecteds.push(parseInt(e.id)) }else{ if(index !== -1) this.selecteds.splice(index, 1) }
                            }
                        });
                    },
                    toggleSelect (option) {
                        let index = this.selecteds.indexOf(option);
                        if(index !== -1) { this.selecteds.splice(index, 1) }else{ this.selecteds.push(option) }
                    },
                    loadData () {
                        let values = [...this.selecteds];
                        this.toggleSelectAll(0);

                        $el.querySelectorAll('[type=checkbox]').forEach((e) => {
                            if(window.getComputedStyle(e.closest('li')).display !== 'none') {
                                let value = parseInt(e.id);
                                if(values.includes(value)) {
                                    e.checked = true;
                                    this.toggleSelect(value);
                                }
                            }
                        });
                    }
                }" @loadf.window="loadData">
                    <div class="flex justify-between">
                        <span class="font-bold text-sm">Funções</span>
                        <div>
                            {{-- <x-button xs primary flat label="Salvar" wire:dirty /> --}}
                            <x-button xs negative flat icon="minus-circle" x-on:click="toggleSelectAll(0)" />
                            <x-button xs positive flat icon="check-circle" x-on:click="toggleSelectAll(1)" />
                        </div>
                    </div>
    
                    <ul class="[&>*]:border-b [&>*:last-child]:border-b-0">
                        @foreach ($roles as $role)
                            <li>
                                <x-checkbox id="{{ $role['id'] }}" label="{{ Str::title($role['name']) }}" name="{{ $role['id'] }}" x-on:click="toggleSelect({{ $role['id'] }})" />
                            </li>
                        @endforeach
                    </ul>
    
                    {{-- <div class="flex justify-end pt-1"><x-button xs primary wire:dirty label="Salvar" /></div> --}}
                </div>
            </div>

            @if(count($user_roles))
                <div class="col-span-full px-2 flex flex-col">
                    @foreach($user_roles as $role)
                        <div x-data="{
                            open: 0
                        }">
                            <div class="flex justify-between items-center cursor-pointer select-none border-b py-2 pr-2" x-on:click="open = !open">
                                <span class="font-bold capitalize text-sm">{{ $role->name }}</span>
                                <span class="transition-transform" :class="open ? 'rotate-180' : ''">
                                    <x-icon name="chevron-down" class="w-3 h-3" />
                                </span>
                            </div>
                        
                            <div class="py-2" x-show="open" x-transition>
                                @if($role->permissions->count())
                                    @foreach ($role->permissions as $permission)
                                        <x-badge label="{{ $permission->name }}" />
                                    @endforeach
                                @else
                                    <span class="text-xs">Vazio.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
     
            {{-- <div class="col-span-1 sm:col-span-2 cursor-pointer bg-gray-100 rounded-xl shadow-md h-72 flex items-center justify-center">
                <div class="flex flex-col items-center justify-center">
                    <x-icon name="cloud-upload" class="w-16 h-16 text-blue-600" />
                    <p class="text-blue-600">Click or drop files here</p>
                </div>
            </div> --}}
     
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Atualizar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
