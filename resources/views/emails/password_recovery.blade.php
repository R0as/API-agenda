<x-mail::message>
# Recuperação de Senha

Olá! Você solicitou a recuperação da sua senha no **Startup de Bolso**.

Utilize o código abaixo no aplicativo para redefinir o seu acesso:

<x-mail::panel>
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
{{ $code }}
</div>
</x-mail::panel>

Se você não solicitou essa alteração, ignore este e-mail.

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
