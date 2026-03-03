# 📅 Startup de Bolso - Gestão Operacional & Agenda

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

Um sistema completo de agendamento e gestão operacional de usuários desenvolvido para a **Startup de Bolso**. Esta aplicação serve como uma API robusta acoplada a um front-end dinâmico e responsivo para gestão de horários, eventos (ensaios, eventos sociais, visitas) e administração da plataforma.

## ✨ Funcionalidades Principais

*   **Autenticação JWT:** Sistema seguro de login e proteção de rotas (API e Web) utilizando tokens JWT (auth-tymon).
*   **Recuperação de Senha por E-mail:** Fluxo completo de envio de código de 6 dígitos via SMTP (Google) para redefinição segura de senha.
*   **Gestão de Agenda (Calendário):** 
    *   Painel interativo (Alpine.js + Tailwind) com visualizações por Mês, Semana e Dia.
    *   Criação, edição e exclusão de eventos com verificações de conflito de horário.
    *   Tipos de eventos customizados: Evento Social, Ensaio Fotográfico e Visita ao Espaço.
*   **Painel Administrativo:** Gestão de usuários do sistema, criação de novos administradores/funcionários (Painel Web responsivo).
*   **Documentação OpenAPI (Swagger):** Toda a API está amplamente documentada e testável via interface do L5-Swagger.

## 🚀 Como Executar o Projeto Localmente

### Pré-requisitos
*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   Banco de dados (MySQL, PostgreSQL ou SQLite)

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/R0as/API-agenda.git
   cd API-agenda
   ```

2. **Instale as dependências do PHP e do Node:**
   ```bash
   composer install
   npm install
   ```

3. **Configuração de Ambiente:**
   Copie o arquivo `.env.example` para `.env` e configure suas variáveis (Banco de Dados, SMTP, etc).
   ```bash
   cp .env.example .env
   ```

4. **Gerar Chaves e JWT Secret:**
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Executar as Migrations e Seeders:**
   *(O Seeder padrão cria um usuário admin admin@admin.com / password)*
   ```bash
   php artisan migrate --seed
   ```

6. **Compilar os Assets (Tailwind):**
   ```bash
   npm run build
   # ou 'npm run dev' durante o desenvolvimento
   ```

7. **Inicie o Servidor Local:**
   ```bash
   php artisan serve
   ```
   > Acesse o sistema na web via: `http://localhost:8000`

## 📧 Configuração de E-mail (Gmail)
Para o sistema de recuperação de senha funcionar, edite as seguintes variáveis no seu `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app-do-google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```
*Obs: É necessário gerar uma [Senha de App no painel de Segurança da Conta Google](https://myaccount.google.com/apppasswords).*

## 📖 Documentação da API (Swagger)

A API possui uma interface interativa baseada na especificação OpenAPI (Swagger).
Para acessá-la, com o servidor rodando, visite:

👉 **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**

(O arquivo estático de configuração JSON fica localizado em `storage/api-docs/api-docs.json`)

## 🛠️ Tecnologias Utilizadas
*   **Backend:** Laravel 11.x
*   **Frontend/UI:** Blade Templates, Tailwind CSS, Alpine.js, Lucide Icons
*   **Autenticação API:** Tymon JWT Auth
*   **Documentação API:** L5-Swagger / OpenAPI 3.0
*   **Banco de Dados:** Laravel Eloquent ORM

---
*Desenvolvido para Startup de Bolso.*
