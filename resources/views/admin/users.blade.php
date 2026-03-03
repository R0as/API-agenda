<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Startup de Bolso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Outfit', sans-serif; }
        .transition-soft { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen" x-data="adminApp()">

    <!-- Header -->
    <header class="bg-white border-b px-4 md:px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-40">
        <div class="flex items-center gap-2 md:gap-4">
            <h1 class="text-lg md:text-xl font-black text-rose-500 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 md:w-6 md:h-6"></i>
                <span class="hidden sm:inline">Admin Area</span>
            </h1>
            <span class="text-xs font-bold text-slate-300">|</span>
            <span class="text-xs md:text-sm font-bold text-slate-500">Gestão <span class="hidden sm:inline">de Usuários</span></span>
        </div>
        <div class="flex items-center gap-4">
            <button @click="logout" class="text-xs md:text-sm font-bold text-slate-400 hover:text-rose-500 transition-soft flex items-center gap-2 cursor-pointer">
                Sair <i data-lucide="log-out" class="w-4 h-4"></i>
            </button>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 md:p-8">
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 md:mb-8 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-slate-800">Usuários</h2>
                <p class="text-xs md:text-sm font-bold text-slate-400">Gerencie o acesso à plataforma.</p>
            </div>
            <button @click="openModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-slate-800 transition-soft active:scale-95 text-sm font-bold cursor-pointer">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Novo Usuário
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-4 md:px-6 py-4 text-[10px] font-black tracking-widest text-slate-400 uppercase">Nome</th>
                            <th class="px-4 md:px-6 py-4 text-[10px] font-black tracking-widest text-slate-400 uppercase hidden sm:table-cell">E-mail</th>
                            <th class="px-4 md:px-6 py-4 text-[10px] font-black tracking-widest text-slate-400 uppercase">Perfil</th>
                            <th class="px-4 md:px-6 py-4 text-[10px] font-black tracking-widest text-slate-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="user in users" :key="user.id">
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-soft group">
                                <td class="px-4 md:px-6 py-4 md:py-5 font-bold text-slate-800 text-sm md:text-base">
                                    <span x-text="user.name"></span>
                                    <div class="text-xs text-slate-500 font-medium sm:hidden mt-0.5" x-text="user.email"></div>
                                </td>
                                <td class="px-4 md:px-6 py-4 md:py-5 text-sm text-slate-500 font-medium hidden sm:table-cell" x-text="user.email"></td>
                                <td class="px-4 md:px-6 py-4 md:py-5">
                                    <span :class="user.role === 'admin' ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500'" class="px-2 md:px-3 py-1 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-wider">
                                        <span x-text="user.role"></span>
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 md:py-5 text-right">
                                    <div class="flex items-center justify-end gap-1 md:gap-2 transition-soft">
                                        <button @click="openModal(user)" class="p-1.5 md:p-2 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-soft cursor-pointer"><i data-lucide="edit-2" class="w-4 h-4"></i></button>
                                        <button @click="deleteUser(user.id)" class="p-1.5 md:p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-soft cursor-pointer"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 font-bold">Nenhum usuário cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div x-show="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden transform transition-soft">
            <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg md:text-xl font-black text-slate-800" x-text="modal.isEdit ? 'Editar Usuário' : 'Novo Usuário'"></h3>
                <button @click="modal.open = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <form @submit.prevent="saveUser" class="p-8 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nome Completo</label>
                    <input type="text" x-model="form.name" class="w-full bg-slate-50 border-none rounded-xl px-5 py-4 text-sm font-bold shadow-inner outline-none focus:bg-white focus:ring-4 focus:ring-slate-100" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">E-mail</label>
                    <input type="email" x-model="form.email" class="w-full bg-slate-50 border-none rounded-xl px-5 py-4 text-sm font-bold shadow-inner outline-none focus:bg-white focus:ring-4 focus:ring-slate-100" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Senha <span x-show="modal.isEdit" class="text-xs lowercase text-slate-300">(deixe em branco para não alterar)</span></label>
                    <input type="password" x-model="form.password" class="w-full bg-slate-50 border-none rounded-xl px-5 py-4 text-sm font-bold shadow-inner outline-none focus:bg-white focus:ring-4 focus:ring-slate-100" :required="!modal.isEdit" minlength="6">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Perfil</label>
                    <select x-model="form.role" class="w-full bg-slate-50 border-none rounded-xl px-5 py-4 text-sm font-bold shadow-inner outline-none focus:bg-white focus:ring-4 focus:ring-slate-100 cursor-pointer">
                        <option value="user">Usuário Comum</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="modal.open = false" class="flex-1 py-4 text-sm font-bold text-slate-400 hover:bg-slate-50 rounded-2xl transition-soft cursor-pointer">Cancelar</button>
                    <button type="submit" :disabled="loading" class="flex-[2] py-4 bg-slate-900 hover:bg-slate-800 text-white font-black text-sm rounded-2xl shadow-lg transition-soft active:scale-95 cursor-pointer disabled:opacity-70 flex justify-center items-center">
                        <span x-show="!loading">Salvar</span>
                        <div x-show="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 bg-slate-900 shadow-2xl text-white px-6 py-4 rounded-2xl border border-white/10">
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="w-2 h-2 rounded-full"></div>
        <span class="text-sm font-bold" x-text="toast.message"></span>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminApp', () => ({
                users: [],
                modal: { open: false, isEdit: false, userId: null },
                form: { name: '', email: '', password: '', role: 'user' },
                loading: false,
                toast: { show: false, message: '', type: 'success' },

                get headers() {
                    const token = localStorage.getItem('jwt_token');
                    if (!token) {
                        window.location.href = '/';
                        return {};
                    }
                    return {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    };
                },

                init() {
                    if (localStorage.getItem('user_role') !== 'admin') {
                        window.location.href = '/agenda';
                        return;
                    }
                    this.fetchUsers();
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async fetchUsers() {
                    try {
                        const res = await fetch('/api/admin/users', { headers: this.headers });
                        if (res.status === 401 || res.status === 403) throw new Error('Acesso negado');
                        this.users = await res.json();
                        this.$nextTick(() => lucide.createIcons());
                    } catch (e) {
                        this.showToast(e.message, 'error');
                        if (e.message === 'Acesso negado') window.location.href = '/';
                    }
                },

                openModal(user = null) {
                    if (user) {
                        this.modal = { open: true, isEdit: true, userId: user.id };
                        this.form = { name: user.name, email: user.email, password: '', role: user.role };
                    } else {
                        this.modal = { open: true, isEdit: false, userId: null };
                        this.form = { name: '', email: '', password: '', role: 'user' };
                    }
                },

                async saveUser() {
                    this.loading = true;
                    try {
                        const method = this.modal.isEdit ? 'PUT' : 'POST';
                        const url = this.modal.isEdit ? `/api/admin/users/${this.modal.userId}` : '/api/admin/users';
                        
                        const res = await fetch(url, {
                            method,
                            headers: this.headers,
                            body: JSON.stringify(this.form)
                        });
                        
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.message || 'Erro ao salvar usuário');
                        }

                        this.showToast('Usuário salvo com sucesso!');
                        this.modal.open = false;
                        this.fetchUsers();
                    } catch(e) {
                        this.showToast(e.message, 'error');
                    }
                    this.loading = false;
                },

                async deleteUser(id) {
                    if (!confirm('Excluir este usuário permanentemente?')) return;
                    try {
                        const res = await fetch(`/api/admin/users/${id}`, {
                            method: 'DELETE',
                            headers: this.headers
                        });
                        
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.error || 'Erro ao excluir usuário');
                        }

                        this.showToast('Usuário excluído!');
                        this.fetchUsers();
                    } catch(e) {
                        this.showToast(e.message, 'error');
                    }
                },

                logout() {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user_role');
                    window.location.href = '/';
                }
            }));
        });
        
        lucide.createIcons();
    </script>
</body>
</html>
