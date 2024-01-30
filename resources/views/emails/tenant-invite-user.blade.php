<p>Prezado {{ $user->name ?: $user->email }},</p>
<p>Espero que este e-mail o encontre bem.</p>
<p>Gostaríamos de estender a você um convite para se juntar à equipe da {{ $tenant_name }}! Após revisarmos seu perfil e suas habilidades, acreditamos que você seria uma excelente adição ao nosso time.</p>
<p>Na {{ $tenant_name }}, estamos comprometidos em oferecer um ambiente de trabalho dinâmico e desafiador, onde cada membro da equipe tem a oportunidade de crescer profissionalmente e contribuir para o sucesso da empresa.</p>
<p>Para aceitar nosso convite e começar sua jornada conosco, basta clicar em <a href="{{ $link_accept }}" target="_blank">Aceitar Convite</a> ou copiando a url abaixo e colando no navegador:<br/><br/>{{ $link_accept }}<br/><br/></p>
<p>Após clicar no link, você será redirecionado para o nosso portal, onde poderá acessar seu painel e iniciar suas atividades conosco.</p>
<p>Estamos ansiosos para receber você em nossa equipe e trabalhar juntos para alcançar grandes conquistas.</p>
<p>Se houver alguma dúvida ou precisar de mais informações, não hesite em entrar em contato conosco.</p>
<p>Atenção: O Convite é valido somente até {{ $expire_at }}</p>
<br>
<p>Atenciosamente,</p>
<p>
    {{ $tenant->users->first()->name }}<br>
    <br>
    CEO<br>
    {{ $tenant_name }}
</p>