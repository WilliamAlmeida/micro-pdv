<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('tenant.dashboard', tenant()) }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link wire:navigate :href="route('tenant.dashboard', tenant())" :active="request()->routeIs('tenant.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link wire:navigate :href="route('tenant.pdv.index', tenant())" :active="request()->routeIs('pdv.index')">
                        {{ __('PDV') }}
                    </x-nav-link>

                    <x-nav-dropdown :active="request()->routeIs(['tenant.categorias.index', 'tenant.produtos.index', 'tenant.fornecedores.index', 'tenant.convenios.index'])">
                        <x-dropdown align="left">
                            <x-slot name="trigger">Cadastros</x-slot>
                            <x-dropdown.item wire:navigate :href="route('tenant.categorias.index', tenant())" :active="request()->routeIs('tenant.categorias.index')" label="{{ __('Categorias') }}" />
                            <x-dropdown.item wire:navigate :href="route('tenant.produtos.index', tenant())" :active="request()->routeIs('tenant.produtos.index')" label="{{ __('Produtos') }}" />
                            <x-dropdown.item wire:navigate :href="route('tenant.fornecedores.index', tenant())" :active="request()->routeIs('tenant.fornecedores.index')" label="{{ __('Fornecedores') }}" />
                            <x-dropdown.item wire:navigate :href="route('tenant.convenios.index', tenant())" :active="request()->routeIs('tenant.convenios.index')" label="{{ __('Convênios') }}" />
                            <x-dropdown.item wire:navigate :href="route('tenant.clientes.index', tenant())" :active="request()->routeIs('tenant.clientes.index')" label="{{ __('Clientes') }}" />
                        </x-dropdown>
                    </x-nav-dropdown>

                    <x-nav-dropdown :active="request()->routeIs(['tenant.relatorios.caixa_operando.index'])">
                        <x-dropdown align="left">
                            <x-slot name="trigger">Relatórios</x-slot>
                            <x-dropdown.item wire:navigate :href="route('tenant.relatorios.caixa_operando.index', tenant())" :active="request()->routeIs('tenant.relatorios.caixa_operando.index')" label="{{ __('Caixa Operando') }}" />
                            {{-- <x-dropdown.item wire:navigate :href="route('tenant.categorias.index', tenant())" :active="request()->routeIs('tenant.categorias.index')" label="{{ __('Total Vendido dos Caixas') }}" /> --}}
                            {{-- <x-dropdown.item wire:navigate :href="route('tenant.categorias.index', tenant())" :active="request()->routeIs('tenant.categorias.index')" label="{{ __('Produtos Vendidos dos Caixas') }}" /> --}}
                        </x-dropdown>
                    </x-nav-dropdown>

                    <x-nav-dropdown :active="request()->routeIs(['tenant.estoque.index'])">
                        <x-dropdown align="left">
                            <x-slot name="trigger">Estoque</x-slot>
                            <x-dropdown.item wire:navigate :href="route('tenant.estoque.index', tenant())" :active="request()->routeIs('tenant.estoque.index')" label="{{ __('Movimentações') }}" />
                        </x-dropdown>
                    </x-nav-dropdown>

                    <x-nav-link wire:navigate :href="route('tenant.usuarios.index', tenant())" :active="request()->routeIs('tenant.usuarios.index')">
                        {{ __('Usuários') }}
                    </x-nav-link>

                    <x-nav-link wire:navigate :href="route('tenant.empresa.edit', tenant())" :active="request()->routeIs('tenant.empresa.edit')">
                        {{ __('Empresa') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="flex mr-3 space-x-3">
                    <x-icon name="sun" class="w-5 h-5 dark:text-white" />
                    <div x-data="{ checked: localStorage.theme === 'dark' }">
                        <x-toggle lg x-on:click="theme = (theme == 'dark' ? 'light' : 'dark')" x-bind:checked="checked" />
                    </div>
                    <x-icon name="moon" class="w-5 h-5 dark:text-white" />
                </div>

                <x-dropdown>
                    <x-slot name="trigger">
                        <x-button label="{{ Auth::user()->name ?? '?' }}" dark rightIcon="dots-vertical" />
                    </x-slot>

                    <x-dropdown.header label="{{ __('Settings') }}">
                        <x-dropdown.item wire:navigate label="{{ __('Manage Account') }}" :href="route('tenant.conta.edit', tenant())" />
                        <x-dropdown.item wire:navigate label="{{ __('Dashboard') }}" :href="route('admin.dashboard')" />
                    </x-dropdown.header>

                    @if(auth()->check() && auth()->user()->isAdmin())
                        <x-dropdown.header label="Admin">
                            <x-dropdown.item label="{{ __('Artisan Panel') }}" onclick="Livewire.dispatch('openArtisanPanel');" />
                        </x-dropdown.header>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown.item label="{{ __('Logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" separator />
                    </form>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('tenant.dashboard', tenant())" :active="request()->routeIs('tenant.dashboard')" label="{{ __('Dashboard') }}" />

                <x-responsive-nav-link wire:navigate :href="route('tenant.pdv.index', tenant())" :active="request()->routeIs('tenant.pdv.index')" label="{{ __('PDV') }}" />
                <x-responsive-nav-link wire:navigate :href="route('tenant.usuarios.index', tenant())" :active="request()->routeIs('tenant.usuarios.index')" label="{{ __('Usuários') }}" />

                <x-responsive-nav-dropdown
                    label="{{ __('Cadastros') }}"
                    toggleIcon
                    :active="request()->routeIs(['tenant.categorias.index', 'tenant.produtos.index', 'tenant.fornecedores.index', 'tenant.convenios.index'])"
                    >
                    <x-responsive-nav-link wire:navigate :href="route('tenant.categorias.index', tenant())" :active="request()->routeIs('tenant.categorias.index')" label="{{ __('Categorias') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('tenant.produtos.index', tenant())" :active="request()->routeIs('tenant.produtos.index')" label="{{ __('Produtos') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('tenant.fornecedores.index', tenant())" :active="request()->routeIs('tenant.fornecedores.index')" label="{{ __('Fornecedores') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('tenant.convenios.index', tenant())" :active="request()->routeIs('tenant.convenios.index')" label="{{ __('Convênios') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('tenant.clientes.index', tenant())" :active="request()->routeIs('tenant.clientes.index')" label="{{ __('Clientes') }}" />
                </x-responsive-nav-dropdown>

                <x-responsive-nav-link wire:navigate :href="route('tenant.estoque.index', tenant())" :active="request()->routeIs('tenant.estoque.index')" label="{{ __('Movimentações') }}" />

                {{-- <x-responsive-nav-dropdown
                    label="{{ __('Tributações') }}"
                    toggleIcon
                    :active="request()->routeIs(['ncms.index', 'cests.index', 'cfops.index'])"
                    >
                    <x-responsive-nav-link wire:navigate :href="route('ncms.index')" :active="request()->routeIs('ncms.index')" label="{{ __('Ncm') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('cests.index')" :active="request()->routeIs('cests.index')" label="{{ __('Cest') }}" />
                    <x-responsive-nav-link wire:navigate :href="route('cfops.index')" :active="request()->routeIs('cfops.index')" label="{{ __('Cfop') }}" />
                </x-responsive-nav-dropdown> --}}
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name ?? '?' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email ?? '?' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('tenant.conta.edit', tenant())">
                    {{ __('Manage Account') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
