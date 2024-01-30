<x-mail::message>
    Prezado <strong>{{ $user->name ?: $user->email }}</strong>,<br/><br/>
    Espero que este e-mail o encontre bem.<br/><br/>
    Gostaríamos de estender a você um convite para se juntar à equipe da {{ $tenant_name }}! Após revisarmos seu perfil e suas habilidades, acreditamos que você seria uma excelente adição ao nosso time.<br/><br/>
    Na {{ $tenant_name }}, estamos comprometidos em oferecer um ambiente de trabalho dinâmico e desafiador, onde cada membro da equipe tem a oportunidade de crescer profissionalmente e contribuir para o sucesso da empresa.<br/><br/>
    Para aceitar nosso convite e começar sua jornada conosco, basta clicar em:
    <x-mail::button :url="$link_accept" color="primary">Aceitar Convite</x-mail::button>
    Após clicar no link, você será redirecionado para o nosso portal, onde poderá acessar seu painel e iniciar suas atividades conosco.<br/><br/>
    Estamos ansiosos para receber você em nossa equipe e trabalhar juntos para alcançar grandes conquistas.<br/><br/>
    Se houver alguma dúvida ou precisar de mais informações, não hesite em entrar em contato conosco.<br/><br/>
    Atenção: O Convite é valido somente até <strong>{{ $expire_at }}</strong><br/><br/>
    Atenciosamente,<br/>
    {{ config('app.name') }}

    <x-slot:subcopy>
                @lang(
                    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                    'into your web browser:',
                    [
                        'actionText' => 'Aceitar Convite',
                    ]
                ) <span class="break-all">[{{ $link_accept }}]({{ $link_accept }})</span>
            </x-slot:subcopy>
</x-mail::message>