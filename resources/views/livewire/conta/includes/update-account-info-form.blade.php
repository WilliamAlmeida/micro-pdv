<div class="flex flex-col gap-4">
    <h2 class="font-bold text-lg">Informação do Perfil</h2>
    <p>Atualize as informações do perfil da sua conta e o endereço de e-mail.</p>

    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>
        <div>
            <p class="text-sm mb-2 text-gray-800 dark:text-gray-200">
                {{ __('Your email address is unverified.') }}
                
                <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    {{ __('Click here to re-send the verification email.') }}
                </button>
            </p>
            
            @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
            @endif
        </div>
    @endif

    <form class="flex flex-col gap-4" wire:submit="atualizar_conta">
        <x-input label="Nome" placeholder="Nome" wire:model="name" />
        <x-input label="E-mail" placeholder="E-mail" wire:model="email" type="email" />


        <div class="flex justify-end"><x-button primary label="Salvar" type="submit" /></div>
    </form>
</div>