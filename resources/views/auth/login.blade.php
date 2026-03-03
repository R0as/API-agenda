<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar no Sistema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-blue-500 selection:text-white" x-data="authApp()">
    
    <div class="w-full max-w-[360px] relative" x-cloak>
        <!-- TOAST -->
        <div x-show="toast.show" x-transition class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-gray-900 shadow-xl text-white px-5 py-3 rounded-md min-w-[280px]">
            <div :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'" class="w-2 h-2 rounded-full"></div>
            <span class="text-sm font-medium" x-text="toast.message"></span>
        </div>

        <div class="mb-8 pl-1">
            <div class="flex justify-center w-full mb-8">
                <img src="/logo.png" alt="Startup de Bolso" class="h-20 w-auto opacity-90" onerror="this.style.display='none'">
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1" x-text="subtexts[view]"></h3>
            <h1 class="text-gray-900 text-[26px] font-bold tracking-tight" x-text="titles[view]"></h1>
        </div>

        <!-- LOGIN FORM -->
        <form x-show="view === 'login'" @submit.prevent="login" x-transition.opacity.duration.300ms class="space-y-6">
            
            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'login_email' ? 'text-blue-500' : (loginForm.email ? 'text-gray-500' : 'text-gray-400')">
                    E-mail
                </label>
                <input type="email" x-model="loginForm.email" @focus="activeField = 'login_email'" @blur="activeField = null" 
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-sm text-gray-800 outline-none pr-12 transition-colors cursor-text focus:ring-0 focus:border-blue-500" 
                       :class="activeField === 'login_email' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="example@email.com" required>
                <i data-lucide="mail" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 transition-colors pointer-events-none" :class="activeField === 'login_email' ? 'text-blue-500' : 'text-gray-300'"></i>
            </div>

            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'login_password' ? 'text-blue-500' : (loginForm.password ? 'text-gray-500' : 'text-gray-400')">
                    Password
                </label>
                <input :type="showPassword ? 'text' : 'password'" x-model="loginForm.password" @focus="activeField = 'login_password'" @blur="activeField = null" 
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-lg tracking-[0.2em] text-gray-800 outline-none pr-12 transition-colors cursor-text focus:ring-0 focus:border-blue-500 font-serif" 
                       :class="activeField === 'login_password' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="••••••••" required>
                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 p-1 focus:outline-none cursor-pointer transition-colors group">
                    <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-5 h-5 group-hover:text-gray-500 transition-colors" :class="activeField === 'login_password' ? 'text-blue-500' : 'text-gray-300'"></i>
                </button>
            </div>
            
            <div class="flex justify-end mt-2 pr-1">
                <button type="button" @click="view = 'forgot1'; activeField = null" class="text-sm font-semibold text-blue-500 hover:text-blue-600 transition-colors cursor-pointer bg-transparent border-none">
                    Esqueceu a senha?
                </button>
            </div>

            <button type="submit" :disabled="loading" class="w-full mt-2 bg-[#3b82f6] hover:bg-blue-600 text-white font-medium py-3 rounded-md transition-colors cursor-pointer flex items-center justify-center disabled:opacity-70 h-12 shadow-sm">
                <span x-show="!loading" class="text-[15px]">Entrar</span>
                <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            </button>
        </form>

        <!-- FORGOT PASSWORD (STEP 1) -->
        <form x-show="view === 'forgot1'" @submit.prevent="sendCode" x-transition.opacity.duration.300ms class="space-y-6">
            <p class="text-sm text-gray-500 mb-6 pl-1">Informe seu e-mail para receber o código de recuperação.</p>
            
            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'forgot_email' ? 'text-blue-500' : (forgotForm.email ? 'text-gray-500' : 'text-gray-400')">
                    E-mail
                </label>
                <input type="email" x-model="forgotForm.email" @focus="activeField = 'forgot_email'" @blur="activeField = null" 
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-sm text-gray-800 outline-none pr-12 transition-colors cursor-text focus:ring-0 focus:border-blue-500" 
                       :class="activeField === 'forgot_email' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="example@email.com" required>
                <i data-lucide="mail" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none transition-colors" :class="activeField === 'forgot_email' ? 'text-blue-500' : 'text-gray-300'"></i>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" @click="view = 'login'; activeField = null" class="w-1/3 py-3 text-[15px] font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-md transition-colors cursor-pointer shadow-sm">
                    Voltar
                </button>
                <button type="submit" :disabled="loading" class="flex-1 bg-[#3b82f6] hover:bg-blue-600 text-white py-3 rounded-md text-[15px] font-medium transition-colors cursor-pointer flex items-center justify-center disabled:opacity-70 shadow-sm">
                    <span x-show="!loading">Enviar Código</span>
                    <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </div>
        </form>

        <!-- FORGOT PASSWORD (STEP 2: RESET) -->
        <form x-show="view === 'forgot2'" @submit.prevent="resetPassword" x-transition.opacity.duration.300ms class="space-y-6">
            <p class="text-[13px] font-medium text-green-700 bg-green-50 px-4 py-3 rounded-md border border-green-200 mb-6">
                Código de 6 dígitos enviado para seu e-mail!
            </p>
            
            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'reset_code' ? 'text-blue-500' : (resetForm.code ? 'text-gray-500' : 'text-gray-400')">
                    Código de Recuperação
                </label>
                <input type="text" x-model="resetForm.code" @focus="activeField = 'reset_code'" @blur="activeField = null" maxlength="6"
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-center tracking-[0.5em] font-bold text-gray-800 outline-none transition-colors cursor-text focus:ring-0 focus:border-blue-500" 
                       :class="activeField === 'reset_code' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="000000" required>
            </div>

            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'reset_password' ? 'text-blue-500' : (resetForm.password ? 'text-gray-500' : 'text-gray-400')">
                    Nova Senha
                </label>
                <input :type="showPassword ? 'text' : 'password'" x-model="resetForm.password" @focus="activeField = 'reset_password'" @blur="activeField = null" 
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-lg tracking-[0.2em] text-gray-800 outline-none pr-12 transition-colors cursor-text focus:ring-0 focus:border-blue-500 font-serif" 
                       :class="activeField === 'reset_password' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="••••••••" required minlength="6">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 p-1 focus:outline-none cursor-pointer transition-colors group">
                    <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-5 h-5 group-hover:text-gray-500 transition-colors" :class="activeField === 'reset_password' ? 'text-blue-500' : 'text-gray-300'"></i>
                </button>
            </div>

            <div class="relative">
                <label class="absolute -top-2.5 left-3 bg-white px-1 text-xs font-medium transition-colors cursor-text" 
                       :class="activeField === 'reset_password_confirmation' ? 'text-blue-500' : (resetForm.password_confirmation ? 'text-gray-500' : 'text-gray-400')">
                    Confirme a Senha
                </label>
                <input :type="showPassword ? 'text' : 'password'" x-model="resetForm.password_confirmation" @focus="activeField = 'reset_password_confirmation'" @blur="activeField = null" 
                       class="w-full bg-transparent border rounded-md px-4 py-3 text-lg tracking-[0.2em] text-gray-800 outline-none pr-12 transition-colors cursor-text focus:ring-0 focus:border-blue-500 font-serif" 
                       :class="activeField === 'reset_password_confirmation' ? 'border-blue-500' : 'border-gray-300'" 
                       placeholder="••••••••" required minlength="6">
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" @click="view = 'login'; activeField = null" class="w-1/3 py-3 text-[15px] font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-md transition-colors cursor-pointer shadow-sm">
                    Cancelar
                </button>
                <button type="submit" :disabled="loading" class="flex-1 bg-[#3b82f6] hover:bg-blue-600 text-white py-3 rounded-md text-[15px] font-medium transition-colors cursor-pointer flex items-center justify-center disabled:opacity-70 shadow-sm">
                    <span x-show="!loading">Redefinir</span>
                    <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('authApp', () => ({
                view: 'login', // login, forgot1, forgot2
                loading: false,
                activeField: null,
                showPassword: false,
                toast: { show: false, message: '', type: '' },
                loginForm: { email: '', password: '' },
                forgotForm: { email: '' },
                resetForm: { code: '', password: '', password_confirmation: '', email: '' },
                
                titles: {
                    login: 'Entrar no Sistema',
                    forgot1: 'Recuperar Acesso',
                    forgot2: 'Nova Senha'
                },
                subtexts: {
                    login: 'Acesse sua conta',
                    forgot1: 'Esqueceu sua senha?',
                    forgot2: 'Quase lá'
                },

                init() {
                    if (localStorage.getItem('jwt_token')) {
                        window.location.href = '/agenda';
                    }
                    this.$watch('view', () => {
                        this.showPassword = false;
                        this.$nextTick(() => lucide.createIcons());
                    });
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async login() {
                    this.loading = true;
                    try {
                        const res = await fetch('/api/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.loginForm)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.error || 'Credenciais inválidas');
                        
                        localStorage.setItem('jwt_token', data.access_token);
                        localStorage.setItem('user_role', data.user.role);
                        
                        this.showToast('Login efetuado!');
                        setTimeout(() => {
                            window.location.href = data.user.role === 'admin' ? '/admin' : '/agenda';
                        }, 500);

                    } catch(e) {
                        this.showToast(e.message, 'error');
                    }
                    this.loading = false;
                },

                async sendCode() {
                    this.loading = true;
                    try {
                        const res = await fetch('/api/password/email', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.forgotForm)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Erro ao enviar código');
                        
                        this.resetForm.email = this.forgotForm.email;
                        this.showToast('Código enviado, verifique!');
                        this.view = 'forgot2';
                    } catch(e) {
                        this.showToast(e.message, 'error');
                    }
                    this.loading = false;
                },

                async resetPassword() {
                    if (this.resetForm.password !== this.resetForm.password_confirmation) {
                        this.showToast('As senhas não coincidem', 'error');
                        return;
                    }
                    this.loading = true;
                    try {
                        const res = await fetch('/api/password/reset', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.resetForm)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.error || data.message || 'Erro ao definir nova senha');
                        
                        this.showToast('Senha alterada! Faça login.', 'success');
                        this.loginForm.email = this.resetForm.email;
                        this.loginForm.password = '';
                        this.view = 'login';
                    } catch(e) {
                        this.showToast(e.message, 'error');
                    }
                    this.loading = false;
                }
            }));
        });
        
        lucide.createIcons();
    </script>
</body>
</html>
