<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link wire:navigate :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Painel de Controle') }}
                    </x-nav-link>
                    <x-nav-link wire:navigate :href="route('usuarios.index')" :active="request()->routeIs('usuarios.index')">
                        {{ __('Usuários') }}
                    </x-nav-link>

                    <x-nav-dropdown :active="request()->routeIs(['categorias.index', 'produtos.index', 'fornecedores.index', 'convenios.index'])">
                        <x-dropdown align="left">
                            <x-slot name="trigger">Cadastros</x-slot>
                            <x-dropdown.item wire:navigate :href="route('categorias.index')" :active="request()->routeIs('categorias.index')" label="{{ __('Categorias') }}" />
                            <x-dropdown.item wire:navigate :href="route('produtos.index')" :active="request()->routeIs('produtos.index')" label="{{ __('Produtos') }}" />
                            <x-dropdown.item wire:navigate :href="route('fornecedores.index')" :active="request()->routeIs('fornecedores.index')" label="{{ __('Fornecedores') }}" />
                            <x-dropdown.item wire:navigate :href="route('convenios.index')" :active="request()->routeIs('convenios.index')" label="{{ __('Convênios') }}" />
                            <x-dropdown.item wire:navigate :href="route('clientes.index')" :active="request()->routeIs('clientes.index')" label="{{ __('Clientes') }}" />
                        </x-dropdown>
                    </x-nav-dropdown>

                    <x-nav-dropdown :active="request()->routeIs(['ncms.index', 'cests.index', 'cfops.index'])">
                        <x-dropdown align="left">
                            <x-slot name="trigger">Tributações</x-slot>
                            <x-dropdown.item wire:navigate :href="route('ncms.index')" :active="request()->routeIs('ncms.index')" label="{{ __('Ncm') }}" />
                            <x-dropdown.item wire:navigate :href="route('cests.index')" :active="request()->routeIs('cests.index')" label="{{ __('Cest') }}" />
                            <x-dropdown.item wire:navigate :href="route('cfops.index')" :active="request()->routeIs('cfops.index')" label="{{ __('Cfop') }}" />
                        </x-dropdown>
                    </x-nav-dropdown>

                    @if(auth()->user()->empresa)
                        <x-nav-link wire:navigate :href="route('empresa.edit')" :active="request()->routeIs('empresa.edit')">
                            {{ __('Empresa') }}
                        </x-nav-link>
                    @else
                        <x-nav-link wire:navigate :href="route('empresa.create')" :active="request()->routeIs('empresa.create')">
                            {{ __('Empresa') }}
                        </x-nav-link>
                    @endif
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
                        <x-button label="{{ Auth::user()->name }}" dark rightIcon="dots-vertical" />
                    </x-slot>
                 
                    <x-dropdown.item wire:navigate label="{{ __('Profile') }}" :href="route('profile.edit')" />
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown.item label="{{ __('Logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" />
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
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
