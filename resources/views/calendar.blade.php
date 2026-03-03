<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Startup de Bolso - Gestão Profissional</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Outfit', sans-serif; }
        .calendar-grid-month { grid-template-rows: repeat(6, 1fr); min-height: 500px; }
        .calendar-grid-week { grid-template-rows: auto; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        button, .clickable { cursor: pointer !important; }
        .transition-soft { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .select-custom { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem; }
        .mobile-fab { position: fixed; bottom: 40px; right: 24px; z-index: 100; }
        .mobile-bottom-bar { position: fixed; bottom: 48px; left: 24px; right: 96px; z-index: 90; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 overflow-hidden" x-data="calendarApp()" x-init="init()" x-cloak>
    <div class="flex h-screen flex-col">
        <!-- Header (Desktop) -->
        <header class="hidden lg:flex items-center justify-between border-b px-8 py-5 bg-white z-[60] shadow-sm">
            <div class="flex items-center gap-10">
                <div class="flex items-center gap-4">
                    <img src="/logo.png" alt="Startup de Bolso" class="h-14 w-auto">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-900 leading-tight">Startup de Bolso</h1>
                        <p class="text-[9px] text-slate-400 uppercase tracking-[0.3em] font-bold">Gestão Operacional</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
                        <button @click="goToToday()" class="px-5 py-1.5 hover:bg-white hover:shadow-sm rounded-lg transition-soft text-xs font-bold text-slate-600">Hoje</button>
                        <button @click="logout()" class="px-5 py-1.5 hover:bg-white hover:shadow-sm rounded-lg transition-soft text-xs font-bold text-rose-500">Sair</button>
                        <div class="flex items-center gap-1 px-1">
                            <button @click="prev()" class="p-1.5 hover:bg-white hover:shadow-sm rounded-lg transition-soft text-slate-500"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                            <button @click="next()" class="p-1.5 hover:bg-white hover:shadow-sm rounded-lg transition-soft text-slate-500"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 font-bold">
                        <select x-model="viewDate.month" @change="updateFromSelectors()" class="select-custom text-lg font-bold text-slate-800 bg-transparent border-none focus:ring-0 pl-0 pr-8 cursor-pointer hover:text-slate-600 transition-soft">
                            @foreach(['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'] as $index => $month)
                                <option value="{{ $index }}">{{ $month }}</option>
                            @endforeach
                        </select>
                        <select x-model="viewDate.year" @change="updateFromSelectors()" class="select-custom text-lg font-medium text-slate-400 bg-transparent border-none focus:ring-0 pl-0 pr-8 cursor-pointer hover:text-slate-600 transition-soft">
                            @for($y = 2026; $y <= 2035; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <button @click="searchOpen = true" class="p-2 hover:bg-slate-100 rounded-xl text-slate-500 transition-soft">
                <i data-lucide="search" class="w-5 h-5"></i>
            </button>
        </header>

        <!-- Header (Mobile) -->
        <header class="flex lg:hidden items-center justify-between px-5 py-4 bg-white z-[60] border-b border-slate-100">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="text-center">
                <h1 class="text-base font-black text-slate-900 uppercase tracking-widest" x-text="months_pt[viewDate.month] + ' ' + viewDate.year"></h1>
            </div>
            <button @click="searchOpen = true" class="p-2 text-slate-600">
                <i data-lucide="search" class="w-6 h-6"></i>
            </button>
        </header>

        <div class="flex flex-1 overflow-hidden relative">
            <!-- Sidebar (Mobile Drawer) -->
            <aside 
                :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 w-72 md:w-80 border-r bg-white p-8 flex flex-col z-50 transition-transform duration-300 ease-in-out lg:static shadow-2xl lg:shadow-none"
            >
                <div class="flex items-center justify-between mb-8 lg:hidden">
                    <span class="font-black text-slate-800">Menu</span>
                    <button @click="mobileMenuOpen = false" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <button @click="openModal()" class="flex items-center justify-center gap-3 w-full py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl shadow-xl shadow-slate-200 transition-soft active:scale-95 font-bold text-sm mb-10 transform hover:-translate-y-0.5">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Novo Registro
                </button>
                
                <!-- Scrollable Sidebar Content -->
                <div 
                    @touchstart="touchStartX = $event.touches[0].clientX"
                    @touchend="handleSwipe($event.changedTouches[0].clientX)"
                    class="flex-1 space-y-8 overflow-y-auto custom-scrollbar pr-2"
                >
                    <!-- Date Navigation (Mobile only) -->
                    <div class="lg:hidden">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 block mb-3">Período</label>
                        <div class="flex items-center gap-2 mb-3">
                            <select x-model="viewDate.month" @change="updateFromSelectors(); mobileMenuOpen = false" class="flex-1 bg-slate-100 border-none rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-slate-200 outline-none cursor-pointer">
                                @foreach(['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'] as $index => $month)
                                    <option value="{{ $index }}">{{ $month }}</option>
                                @endforeach
                            </select>
                            <select x-model="viewDate.year" @change="updateFromSelectors(); mobileMenuOpen = false" class="w-24 bg-slate-100 border-none rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-slate-200 outline-none cursor-pointer">
                                @for($y = 2026; $y <= 2035; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="prev(); mobileMenuOpen = false" class="flex-1 flex items-center justify-center gap-1 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-600 transition-soft">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i> Anterior
                            </button>
                            <button @click="goToToday(); mobileMenuOpen = false" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-soft">Hoje</button>
                            <button @click="next(); mobileMenuOpen = false" class="flex-1 flex items-center justify-center gap-1 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-600 transition-soft">
                                Próximo <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- View Selector -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Visualização</label>
                        <div class="space-y-1">
                            <template x-for="view in [{id:'day', name:'Dia', icon:'calendar-days'}, {id:'week', name:'Semana', icon:'calendar-range'}, {id:'month', name:'Mês', icon:'calendar'}, {id:'year', name:'Ano', icon:'calendar-check'}]">
                                <button @click="currentView = view.id; mobileMenuOpen = false" 
                                        :class="currentView === view.id ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50'"
                                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-soft font-bold text-xs"
                                >
                                    <i :data-lucide="view.icon" class="w-4 h-4"></i>
                                    <span x-text="view.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Próximos Eventos</h2>
                        <div class="space-y-4">
                            <template x-for="event in events.slice(0, 5)" :key="event.id">
                                <div @click="editEvent(event)" class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-slate-300 hover:bg-white transition-soft cursor-pointer group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="formatDateShort(event.start_time)"></span>
                                        <div :style="'background-color: ' + (colorMap[event.color] || colorMap.slate)" class="w-2 h-2 rounded-full"></div>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 group-hover:text-slate-900 truncate" x-text="event.title"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 mt-auto">
                    <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest text-center">Startup de Bolso v1.0</p>
                </div>
            </aside>

            <!-- Mobile Sidebar Backdrop -->
            <div 
                x-show="mobileMenuOpen" 
                @click="mobileMenuOpen = false"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 text-white"
            ></div>

            <!-- Main Calendar Area -->
            <main class="flex-1 flex flex-col bg-white overflow-hidden relative">
                <!-- Day Headers (Desktop) — only shown in Month view -->
                <div class="hidden lg:grid grid-cols-7 border-b bg-slate-50/50" x-show="currentView === 'month'">
                    <template x-for="day in ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB']">
                        <div class="py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="day"></div>
                    </template>
                </div>

                <!-- Day Headers (Mobile) — only shown in Month view -->
                <div class="grid lg:hidden grid-cols-7 border-b bg-white" x-show="currentView === 'month'">
                    <template x-for="day in ['D', 'S', 'T', 'Q', 'Q', 'S', 'S']">
                        <div class="py-2 text-center text-[10px] font-black text-slate-300 uppercase" x-text="day"></div>
                    </template>
                </div>

                <!-- Scrollable Content -->
                <div 
                    @touchstart="touchStartX = $event.touches[0].clientX"
                    @touchend="handleSwipe($event.changedTouches[0].clientX)"
                    class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30"
                >
                    
                    <!-- MONTH VIEW -->
                    <div x-show="currentView === 'month'" class="grid grid-cols-7 calendar-grid-month border-l border-t border-slate-50 select-none">
                        <template x-for="day in calendarDays">
                            <div 
                                @click="day.isCurrentMonth ? handleDayClick(day.date) : null"
                                :class="{
                                    'cursor-pointer': day.isCurrentMonth,
                                    'bg-orange-50 hover:bg-orange-100/60': day.isCurrentMonth && getHoliday(day.date),
                                    'bg-white hover:bg-slate-50/50': day.isCurrentMonth && !getHoliday(day.date),
                                    'bg-slate-50/50 grayscale opacity-30': !day.isCurrentMonth
                                }"
                                class="border-r border-b p-1.5 md:p-4 transition-soft relative group min-h-[80px] md:min-h-[130px] min-w-0"
                            >
                                <div class="flex flex-col items-center">
                                    <div 
                                        :class="{
                                            'bg-slate-900 text-white shadow-lg': day.isToday,
                                            'text-orange-600 font-black': !day.isToday && getHoliday(day.date) && day.isCurrentMonth,
                                            'text-slate-800 font-black': !day.isToday && !getHoliday(day.date) && day.isCurrentMonth,
                                            'text-slate-200': !day.isCurrentMonth
                                        }"
                                        style="min-width: 2rem; min-height: 2rem;"
                                        class="text-sm md:text-lg w-8 h-8 md:w-11 md:h-11 aspect-square font-black flex items-center justify-center rounded-full transition-soft mb-1 mx-auto" 
                                        x-text="day.dayNumber"
                                    ></div>
                                    
                                    <!-- Event indicators (Mobile) -->
                                    <div class="lg:hidden flex gap-1 mt-1 flex-wrap justify-center px-1">
                                        <template x-if="getHoliday(day.date) && day.isCurrentMonth">
                                            <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                                        </template>
                                        <template x-for="event in eventsForDay(day.date).slice(0, 3)">
                                            <div :style="'background-color: ' + (colorMap[event.color] || colorMap.slate)" class="w-1.5 h-1.5 rounded-full"></div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Holiday badge (Desktop) -->
                                <template x-if="getHoliday(day.date) && day.isCurrentMonth">
                                    <div class="hidden lg:flex items-center gap-1 mt-2 mb-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full text-[9px] font-bold truncate max-w-full">
                                            🎉 <span x-text="getHoliday(day.date).name"></span>
                                        </span>
                                    </div>
                                </template>

                                <!-- Event list (Desktop) -->
                                <div class="hidden lg:flex flex-col gap-1 w-full">
                                    <template x-for="event in eventsForDay(day.date)" :key="event.id">
                                        <div 
                                            @click.stop="editEvent(event)"
                                            :style="'background-color: ' + (colorMap[event.color] || colorMap.slate) + '40; color: ' + (colorMap[event.color] || colorMap.slate) + '; border-color: ' + (colorMap[event.color] || colorMap.slate) + '90'"
                                            class="px-2 py-1 rounded-lg text-[10px] font-bold truncate border transition-soft hover:shadow-sm"
                                            x-text="event.title"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- WEEK VIEW (Vertical Timeline) -->
                    <div x-show="currentView === 'week'" class="flex flex-col flex-1 overflow-auto custom-scrollbar bg-white">
                        <div class="min-w-[700px] lg:min-w-0 flex flex-col flex-1">
                            <div class="flex border-b sticky top-0 bg-white z-40">
                                <div class="w-12 md:w-16 flex-shrink-0 border-r bg-slate-50/50 sticky left-0 z-50"></div>
                                <div class="grid grid-cols-7 flex-1">
                                    <template x-for="day in weekDays">
                                        <div :class="getHoliday(day.date) ? 'bg-amber-50' : ''" class="py-4 text-center border-r last:border-r-0">
                                            <div class="text-[10px] md:text-sm font-black text-slate-400 uppercase mb-1" x-text="day.name.substring(0, 3)"></div>
                                            <div :class="day.isToday ? 'bg-slate-900 text-white shadow-lg' : (getHoliday(day.date) ? 'bg-amber-100 text-amber-700 ring-2 ring-amber-400' : 'text-slate-800')" class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm md:text-base font-black" x-text="day.dayNumber"></div>
                                            <template x-if="getHoliday(day.date)">
                                                <div class="text-[8px] font-bold text-amber-500 mt-1 px-1 truncate" x-text="getHoliday(day.date).name"></div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="flex relative">
                                <!-- Time column -->
                                <div class="w-12 md:w-16 border-r flex-shrink-0 bg-slate-50/50 sticky left-0 z-30">
                                    <template x-for="hour in Array.from({length: 15}, (_, i) => i + 8)">
                                        <div class="h-20 border-b flex items-start justify-center pt-2">
                                            <span class="text-[9px] md:text-[10px] font-bold text-slate-400" x-text="hour + 'h'"></span>
                                        </div>
                                    </template>
                                </div>
                                <!-- Days columns -->
                            <div class="grid grid-cols-7 flex-1 relative">
                                <template x-for="day in weekDays">
                                    <div class="relative border-r last:border-r-0 h-full min-h-[1200px]">
                                        <template x-for="event in eventsForDay(day.date)">
                                             <div 
                                                 @click.stop="editEvent(event)"
                                                 :style="getEventStyle(event)"
                                                 class="absolute left-1 right-1 rounded-lg p-2 text-xs font-bold border transition-soft hover:shadow-lg overflow-hidden z-10"
                                             >
                                                 <p x-text="event.title" class="truncate text-xs font-black"></p>
                                                 <p class="opacity-60 text-[10px] truncate mt-0.5" x-text="event.start_time.split(' ')[1].substring(0,5) + (event.customer_name ? ' – ' + event.customer_name : '')"></p>
                                                 <template x-if="event.event_type">
                                                     <p class="opacity-50 text-[9px] truncate" x-text="getEventTypeLabel(event.event_type)"></p>
                                                 </template>
                                             </div>
                                        </template>
                                    </div>
                                </template>
                                <!-- Current time indicator -->
                                <div x-show="isCurrentWeek()" :style="'top: ' + getCurrentTimePosition() + 'px'" class="absolute left-0 right-0 border-t-2 border-rose-500 z-30 pointer-events-none flex items-center">
                                    <div class="w-2 h-2 bg-rose-500 rounded-full -ml-1"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DAY VIEW (Vertical Timeline) -->
                    <div x-show="currentView === 'day'" class="flex flex-col flex-1 overflow-y-auto custom-scrollbar bg-white">
                        <div class="p-6 border-b sticky top-0 bg-white z-20 flex items-center gap-6">
                            <div :class="'w-14 h-14 bg-slate-900 text-white rounded-2xl flex flex-col items-center justify-center shadow-lg'">
                                <span class="text-[8px] font-black uppercase" x-text="months_pt[viewDate.month].substring(0, 3)"></span>
                                <span class="text-xl font-black" x-text="new Date(currentDate).getDate()"></span>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-slate-900" x-text="getDayOfWeekName(currentDate)"></h2>
                                <p class="text-xs font-bold text-slate-400" x-text="currentMonthYear"></p>
                            </div>
                        </div>
                        <div class="flex relative">
                            <!-- Time column -->
                            <div class="w-16 border-r flex-shrink-0 bg-slate-50/50">
                                <template x-for="hour in Array.from({length: 15}, (_, i) => i + 8)">
                                    <div class="h-20 border-b flex items-start justify-center pt-2">
                                        <span class="text-[10px] font-bold text-slate-400" x-text="hour + ':00'"></span>
                                    </div>
                                </template>
                            </div>
                            <!-- Event area -->
                            <div class="flex-1 relative h-full min-h-[1200px]">
                                <template x-for="event in eventsForDay(formatIsoDate(currentDate))">
                                    <div 
                                        @click.stop="editEvent(event)"
                                        :style="getEventStyle(event)"
                                        class="absolute left-4 right-4 rounded-2xl p-4 text-xs font-bold border shadow-md transition-soft hover:shadow-xl z-10"
                                    >
                                         <div class="flex items-center gap-3">
                                                 <i data-lucide="video" class="w-4 h-4 opacity-40"></i>
                                                 <span class="font-black text-sm" x-text="event.title"></span>
                                             </div>
                                             <p class="opacity-60 text-xs mt-1 ml-7" x-text="event.start_time.split(' ')[1].substring(0,5) + (event.customer_name ? ' – ' + event.customer_name : '') + (event.description ? ' (' + event.description + ')' : '')"></p>
                                             <template x-if="event.event_type">
                                                 <p class="opacity-50 text-xs mt-0.5 ml-7" x-text="getEventTypeLabel(event.event_type)"></p>
                                             </template>
                                    </div>
                                </template>
                                
                                <template x-if="eventsForDay(formatIsoDate(currentDate)).length === 0">
                                    <div @click="openModal(formatIsoDate(currentDate))" class="absolute inset-0 flex flex-col items-center justify-center p-20 text-slate-300 transition-soft cursor-pointer">
                                        <i data-lucide="calendar-range" class="w-12 h-12 mb-4 opacity-20"></i>
                                        <p class="font-bold opacity-40">Nenhum compromisso agendado</p>
                                        <p class="text-xs mt-2 opacity-30">Toque em qualquer lugar para adicionar</p>
                                    </div>
                                </template>

                                <!-- Current time indicator -->
                                <div x-show="isToday(currentDate)" :style="'top: ' + getCurrentTimePosition() + 'px'" class="absolute left-0 right-0 border-t-2 border-rose-500 z-30 pointer-events-none flex items-center">
                                    <div class="w-3 h-3 bg-rose-500 rounded-full -ml-1.5 shadow-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- YEAR VIEW -->
                    <div x-show="currentView === 'year'" class="p-4 md:p-10 bg-white min-h-full">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 md:gap-12">
                            <template x-for="(monthName, index) in months_pt" :key="index">
                                <div @click="viewDate.month = index; currentView = 'month'; updateFromSelectors()" class="p-6 bg-white rounded-[32px] border border-slate-100 hover:border-slate-300 hover:shadow-2xl transition-soft cursor-pointer group">
                                    <h3 class="text-xs font-black text-slate-800 mb-6 uppercase tracking-widest group-hover:text-slate-900" x-text="monthName"></h3>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="d in ['D','S','T','Q','Q','S','S']">
                                            <div class="text-[7px] font-black text-slate-300 text-center uppercase" x-text="d"></div>
                                        </template>
                                        <template x-for="day in getMiniMonthDays(parseInt(viewDate.year), index)">
                                            <div :class="{
                                                'text-slate-900 font-bold': day.isCurrentMonth,
                                                'text-slate-200': !day.isCurrentMonth,
                                                'bg-slate-900 text-white rounded-lg scale-110 shadow-lg': day.isToday
                                            }" class="text-[9px] h-6 flex items-center justify-center transition-soft" x-text="day.dayNumber"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- MODAL: EVENT FORM -->
    <div 
        x-show="modal.open" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-md"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="modal.open = false"
            class="bg-white rounded-t-[40px] md:rounded-[40px] shadow-2xl w-full max-w-xl overflow-hidden transform transition-soft absolute bottom-0 md:relative"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full md:scale-95 md:translate-y-20"
            x-transition:enter-end="opacity-100 translate-y-0 md:scale-100 md:translate-y-0"
        >
            <div class="p-6 md:p-10 pb-2 md:pb-4 flex items-center justify-between">
                <h3 class="text-xl md:text-2xl font-black text-slate-800" x-text="modal.isEdit ? 'Editar Registro' : 'Novo Registro'"></h3>
                <button @click="modal.open = false" class="p-2 md:p-3 hover:bg-slate-100 rounded-2xl text-slate-400 transition-soft"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            
            <form @submit.prevent="saveEvent" class="p-6 md:p-10 pt-4 md:pt-6 space-y-6 md:space-y-8 overflow-y-auto max-h-[80vh] md:max-h-none">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 md:mb-3 ml-1">Descrição</label>
                    <input type="text" x-model="form.title" placeholder="Ex: Atendimento Consultoria" class="w-full text-lg md:text-xl font-bold bg-slate-50 border-none rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 md:px-8 py-4 md:py-5 transition-soft outline-none shadow-inner" required autofocus>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Data</label>
                        <input type="date" x-model="form.date" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Horário</label>
                        <input type="time" x-model="form.startTime" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none">
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Cliente</label>
                        <input type="text" x-model="form.customer_name" placeholder="Nome do Cliente" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Tipo de Agendamento</label>
                        <select x-model="form.event_type" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none cursor-pointer">
                            <option value="">Selecione o tipo...</option>
                            <option value="social_event">Locação para eventos sociais</option>
                            <option value="photo_shoot">Ensaio fotográfico</option>
                            <option value="venue_visit">Agendar visita ao espaço</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Telefone</label>
                            <input type="tel" x-model="form.customer_phone" @input="applyPhoneMask($event)" placeholder="(00) 00000-0000" maxlength="15" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Status de Pagamento</label>
                            <select x-model="form.payment_status" class="w-full bg-slate-50 border-none rounded-xl focus:bg-white focus:ring-4 focus:ring-slate-100 px-6 py-4 text-sm font-bold shadow-inner outline-none cursor-pointer">
                                <option value="pending">Pendente</option>
                                <option value="paid">Pago</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Identificação por Cor</label>
                    <div class="flex gap-3 md:gap-4 py-2 px-1 overflow-x-auto custom-scrollbar">
                        <template x-for="color in ['indigo', 'blue', 'red', 'green', 'yellow', 'purple', 'slate']">
                            <button type="button" @click="form.color = color"
                                :style="'background-color: ' + colorMap[color]"
                                :class="{'ring-4 ring-offset-2 ring-slate-300 scale-110': form.color === color}"
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full flex-shrink-0 transition-soft shadow-sm">
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-3 md:gap-4 pt-4 md:pt-6 pb-10 md:pb-0">
                    <template x-if="modal.isEdit">
                        <button type="button" @click="confirmDeleteAction()" class="p-4 md:p-5 bg-rose-50 text-rose-500 rounded-2xl md:rounded-3xl hover:bg-rose-100 transition-soft active:scale-95"><i data-lucide="trash-2" class="w-6 h-6"></i></button>
                    </template>
                    <button type="button" @click="modal.open = false" class="hidden sm:block flex-1 py-5 text-sm font-bold text-slate-400 hover:text-slate-600 rounded-3xl transition-soft">Cancelar</button>
                    <button type="submit" :disabled="saving" class="flex-[3] bg-slate-900 hover:bg-slate-800 text-white py-4 md:py-5 rounded-xl md:rounded-[24px] text-sm font-black shadow-xl transition-soft active:scale-[0.98]" :class="saving ? 'opacity-70' : ''">
                        <span x-show="!saving" x-text="modal.isEdit ? 'Atualizar' : 'Salvar'"></span>
                        <div x-show="saving" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Floating Action Button -->
    <div class="lg:hidden mobile-fab">
        <button @click="openModal()" class="w-16 h-16 bg-slate-900 text-white rounded-full shadow-2xl flex items-center justify-center active:scale-95 transition-soft">
            <i data-lucide="plus" class="w-8 h-8"></i>
        </button>
    </div>

    <!-- CUSTOM CONFIRMATION -->
    <div x-show="confirm.open" class="fixed inset-0 z-[110] flex items-center justify-center p-6 bg-slate-900/30 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-10 text-center transform transition-soft" x-transition:enter="scale-90 opacity-0">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6"><i data-lucide="alert-circle" class="w-8 h-8"></i></div>
            <h4 class="text-xl font-black text-slate-800 mb-2">Excluir Registro?</h4>
            <p class="text-slate-400 text-sm font-medium mb-10">Esta ação é definitiva e removerá todos os dados permanentemente.</p>
            <div class="flex gap-3">
                <button @click="confirm.open = false" class="flex-1 py-4 text-sm font-bold text-slate-400 hover:bg-slate-50 rounded-2xl transition-soft">Voltar</button>
                <button @click="executeDelete()" class="flex-[1.5] py-4 bg-rose-500 hover:bg-rose-600 text-white font-black text-sm rounded-2xl shadow-lg transition-soft active:scale-95">Sim, Excluir</button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition:enter="translate-y-20 opacity-0" x-transition:leave="translate-y-20 opacity-0" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[150] flex items-center gap-4 bg-slate-900 shadow-2xl text-white px-8 py-4 rounded-2xl border border-white/10 transition-soft">
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="w-2 h-2 rounded-full"></div>
        <span class="text-sm font-bold" x-text="toast.message"></span>
    </div>

    <!-- SEARCH PANEL -->
    <div
        x-show="searchOpen"
        style="display:none; z-index: 200;"
        class="fixed inset-0 flex flex-col bg-white"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
    >
        <!-- Search Header -->
        <div class="flex items-center gap-3 px-6 md:px-10 py-4 border-b border-slate-200 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input
                x-ref="searchInput"
                x-model="searchQuery"
                @keydown.escape="searchOpen = false; searchQuery = ''"
                type="text"
                placeholder="Buscar por cliente ou descrição..."
                class="flex-1 text-base md:text-lg font-semibold rounded-xl px-4 py-2.5 bg-slate-50 border border-slate-200 outline-none focus:ring-2 focus:ring-slate-300 focus:bg-white text-slate-800 placeholder-slate-400 transition-all"
            >
            <button @click="searchOpen = false; searchQuery = ''" class="p-2 hover:bg-slate-100 rounded-xl text-slate-500 transition-soft flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Results -->
        <div class="flex-1 overflow-y-auto px-6 md:px-10 pt-10 md:pt-16 pb-8 md:pb-10 custom-scrollbar">

            <!-- Empty state -->
            <div x-show="searchQuery.trim() === ''" class="flex flex-col items-center justify-center h-64">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <p class="font-bold text-slate-400">Digite para buscar agendamentos</p>
            </div>

            <!-- Has query -->
            <div x-show="searchQuery.trim() !== ''">

                <!-- No results -->
                <div x-show="searchResults().length === 0" class="flex flex-col items-center justify-center h-64">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m10 16 2 2 4-4"/></svg>
                    <p class="font-bold text-slate-400">Nenhum agendamento encontrado</p>
                    <p class="text-sm text-slate-300 mt-1" x-text="'para: ' + searchQuery"></p>
                </div>

                <!-- Results list -->
                <div x-show="searchResults().length > 0">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4"
                       x-text="searchResults().length + (searchResults().length === 1 ? ' resultado' : ' resultados')"></p>
                    <div class="space-y-2">
                        <template x-for="event in searchResults()" :key="event.id">
                            <div
                                @click="editEvent(event); searchOpen = false; searchQuery = ''"
                                class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 hover:border-slate-300 hover:bg-slate-50 transition-soft cursor-pointer group"
                            >
                                <div :style="'background-color: ' + (colorMap[event.color] || colorMap.slate)" class="w-3 h-3 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 text-sm truncate" x-text="event.title"></p>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        <span class="text-xs text-slate-400 font-medium" x-text="formatDateShort(event.start_time)"></span>
                                        <template x-if="event.event_type">
                                            <span class="text-xs text-slate-300">·</span>
                                        </template>
                                        <template x-if="event.event_type">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500" x-text="getEventTypeLabel(event.event_type)"></span>
                                        </template>
                                        <template x-if="event.customer_name">
                                            <span class="text-xs text-slate-300">·</span>
                                        </template>
                                        <template x-if="event.customer_name">
                                            <span class="text-xs text-slate-500 font-semibold" x-text="event.customer_name"></span>
                                        </template>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-300 group-hover:text-slate-500 flex-shrink-0 transition-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function calendarApp() {
            return {
                currentView: 'month',
                mobileMenuOpen: false,
                events: [],
                calendarDays: [],
                weekDays: [],
                currentDate: new Date("{{ now()->format('Y-m-d') }} 12:00:00"),
                viewDate: { month: "{{ now()->month - 1 }}", year: "{{ now()->year }}" },
                months_pt: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                years_list: Array.from({length: 10}, (_, i) => 2026 + i),
                colorMap: {
                    blue: '#3b82f6',
                    red: '#ef4444',
                    green: '#10b981',
                    yellow: '#f59f0b',
                    purple: '#a855f7',
                    slate: '#64748b',
                    indigo: '#6366f1'
                },
                modal: { open: false, isEdit: false, eventId: null },
                confirm: { open: false, eventId: null },
                saving: false,
                toast: { show: false, message: '', type: 'success' },
                form: { title: '', date: '', startTime: '09:00', color: 'slate', description: '', customer_name: '', customer_phone: '', payment_status: 'pending', event_type: '' },
                touchStartX: 0,
                currentMonthYear: '',
                holidays: [],
                loadedHolidayYear: null,
                searchOpen: false,
                searchQuery: '',

                isCurrentWeek() {
                    const today = new Date();
                    if (!this.weekDays || this.weekDays.length === 0) return false;
                    const startOfWeek = new Date(this.weekDays[0].date + " 00:00:00");
                    const endOfWeek = new Date(this.weekDays[6].date + " 23:59:59");
                    return today >= startOfWeek && today <= endOfWeek;
                },

                getCurrentTimePosition() {
                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    if (hours < 8 || hours >= 22) return -100;
                    return (hours - 8) * 80 + (minutes / 60) * 80;
                },

                getEventStyle(event) {
                    const [h, m] = event.start_time.split(' ')[1].split(':').map(Number);
                    const top = (h - 8) * 80 + (m / 60) * 80;
                    const height = 80; 
                    const color = this.colorMap[event.color] || this.colorMap.slate;
                    return `top: ${top}px; height: ${height}px; background-color: ${color}40; color: ${color}; border-color: ${color}90;`;
                },

                isToday(date) {
                    const today = new Date();
                    const d = (date instanceof Date) ? date : new Date(date + " 12:00:00");
                    return d.getDate() === today.getDate() && 
                           d.getMonth() === today.getMonth() && 
                           d.getFullYear() === today.getFullYear();
                },

                get authHeaders() {
                    const token = localStorage.getItem('jwt_token');
                    if (!token) window.location.href = '/';
                    return { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': `Bearer ${token}` };
                },

                logout() {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user_role');
                    window.location.href = '/';
                },

                async init() {
                    if (!localStorage.getItem('jwt_token')) {
                        window.location.href = '/';
                        return;
                    }
                    this.updateCalendar();
                    await Promise.all([this.fetchEvents(), this.fetchHolidays(this.currentDate.getFullYear())]);
                    this.$watch('currentView', () => this.updateCalendar());
                    this.$watch('searchOpen', val => {
                        if (val) this.$nextTick(() => this.$refs.searchInput?.focus());
                    });
                    this.$nextTick(() => {
                        this.updateCalendar();
                        lucide.createIcons();
                    });
                },

                async fetchHolidays(year) {
                    if (this.loadedHolidayYear === year) return;
                    try {
                        const res = await fetch(`https://brasilapi.com.br/api/feriados/v1/${year}`);
                        if (res.ok) {
                            this.holidays = await res.json();
                            this.loadedHolidayYear = year;
                        }
                    } catch (e) { console.error('Holidays fetch error:', e); }
                },

                getHoliday(date) {
                    return this.holidays.find(h => h.date === date) || null;
                },

                searchResults() {
                    const q = this.searchQuery.trim().toLowerCase();
                    if (!q) return [];
                    return this.events.filter(e =>
                        (e.customer_name && e.customer_name.toLowerCase().includes(q)) ||
                        (e.title && e.title.toLowerCase().includes(q))
                    ).sort((a, b) => a.start_time.localeCompare(b.start_time));
                },

                updateCalendar() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    
                    // Sync selectors (keeping as number/string compatible for x-model)
                    this.viewDate.month = month;
                    this.viewDate.year = year;
                    
                    this.currentMonthYear = this.currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }).toUpperCase();

                    if (this.currentView === 'month') {
                        const startOfMonth = new Date(year, month, 1);
                        const lastDayOfMonth = new Date(year, month + 1, 0);
                        const startOffset = (startOfMonth.getDay() === 0) ? 0 : startOfMonth.getDay();
                        const days = [];
                        const prevMonthLastDay = new Date(year, month, 0).getDate();
                        for (let i = startOffset - 1; i >= 0; i--) days.push(this.createDayObj(new Date(year, month - 1, prevMonthLastDay - i), false));
                        for (let i = 1; i <= lastDayOfMonth.getDate(); i++) days.push(this.createDayObj(new Date(year, month, i), true));
                        const remaining = 42 - days.length;
                        for (let i = 1; i <= remaining; i++) days.push(this.createDayObj(new Date(year, month + 1, i), false));
                        this.calendarDays = days;
                    } else if (this.currentView === 'week') {
                        const days = [];
                        const startOfWeek = new Date(this.currentDate);
                        startOfWeek.setDate(this.currentDate.getDate() - this.currentDate.getDay());
                        const names = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
                        for (let i = 0; i < 7; i++) {
                            const d = new Date(startOfWeek);
                            d.setDate(startOfWeek.getDate() + i);
                            const obj = this.createDayObj(d, true);
                            obj.name = names[i];
                            days.push(obj);
                        }
                        this.weekDays = days;
                    } else if (this.currentView === 'year') {
                        // Logic for Year view doesn't need to generate daily objects here
                        // but we sync the selected year
                        this.viewDate.year = year;
                    }
                    this.refreshIcons();
                },

                updateFromSelectors() {
                    const newYear = parseInt(this.viewDate.year);
                    this.currentDate = new Date(newYear, parseInt(this.viewDate.month), 1);
                    this.fetchHolidays(newYear);
                    this.updateCalendar();
                },

                createDayObj(date, isCurrentMonth) {
                    const iso = this.formatIsoDate(date);
                    const todayIso = this.formatIsoDate(new Date());
                    return { date: iso, dayNumber: date.getDate(), isCurrentMonth, isToday: iso === todayIso };
                },

                formatIsoDate(date) {
                    const d = new Date(date);
                    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                },

                async fetchEvents() {
                    try {
                        const res = await fetch('/api/events', { headers: this.authHeaders });
                        if (res.ok) this.events = await res.json();
                        else if (res.status === 401) this.logout();
                    } catch (e) { console.error(e); }
                },

                eventsForDay(date) {
                    return this.events.filter(e => {
                        const datePart = e.start_time ? e.start_time.split(/[ T]/)[0] : '';
                        return datePart === date;
                    });
                },

                applyPhoneMask(e) {
                    let v = e.target.value.replace(/\D/g, "");
                    if (v.length > 11) v = v.substring(0, 11);
                    v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
                    v = v.replace(/(\d)(\d{4})$/, "$1-$2");
                    this.form.customer_phone = v;
                },

                openModal(date = '') {
                    this.modal.isEdit = false;
                    this.form = { title: '', date: date || this.formatIsoDate(new Date()), startTime: '09:00', color: 'slate', description: '', customer_name: '', customer_phone: '', payment_status: 'pending', event_type: '' };
                    this.modal.open = true;
                    this.refreshIcons();
                },

                handleDayClick(date) {
                    if (window.innerWidth < 1024) {
                        this.currentDate = new Date(date + " 12:00:00");
                        this.viewDate.month = this.currentDate.getMonth().toString();
                        this.viewDate.year = this.currentDate.getFullYear().toString();
                        this.currentView = 'day';
                    } else {
                        this.openModal(date);
                    }
                },

                editEvent(event) {
                    this.modal.isEdit = true;
                    this.modal.eventId = event.id;
                    const parts = event.start_time.split(' ');
                    this.form = { title: event.title, date: parts[0], startTime: parts[1].substring(0, 5), color: event.color || 'slate', description: event.description || '', customer_name: event.customer_name || '', customer_phone: event.customer_phone || '', payment_status: event.payment_status || 'pending', event_type: event.event_type || '' };
                    this.modal.open = true;
                    this.refreshIcons();
                },

                async saveEvent() {
                    this.saving = true;
                    const url = this.modal.isEdit ? `/api/events/${this.modal.eventId}` : '/api/events';
                    const method = this.modal.isEdit ? 'PUT' : 'POST';
                    try {
                        let startDate = new Date(`${this.form.date}T${this.form.startTime}:00`);
                        let endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
                        let formattedEnd = `${endDate.getFullYear()}-${String(endDate.getMonth()+1).padStart(2,'0')}-${String(endDate.getDate()).padStart(2,'0')} ${String(endDate.getHours()).padStart(2,'0')}:${String(endDate.getMinutes()).padStart(2,'0')}:00`;

                        const payload = { ...this.form, start_time: `${this.form.date} ${this.form.startTime}:00`, end_time: formattedEnd };
                        const res = await fetch(url, { method, headers: this.authHeaders, body: JSON.stringify(payload) });
                        if (res.ok) {
                            await this.fetchEvents();
                            this.showToast(this.modal.isEdit ? 'Registro atualizado!' : 'Agendamento confirmado!');
                            this.modal.open = false;
                        } else {
                            const errorData = await res.json();
                            this.showToast(errorData.message || 'Horário indisponível ou erro na API', 'error');
                        }
                    } catch (e) { console.error(e); } finally { this.saving = false; }
                },

                confirmDeleteAction() { this.confirm.eventId = this.modal.eventId; this.confirm.open = true; },

                async executeDelete() {
                    try {
                        const res = await fetch(`/api/events/${this.confirm.eventId}`, { method: 'DELETE', headers: this.authHeaders });
                        if (res.ok) { await this.fetchEvents(); this.showToast('Excluído'); this.confirm.open = false; this.modal.open = false; }
                    } catch (e) { console.error(e); }
                },

                prev() {
                    let d = new Date(this.currentDate);
                    if (this.currentView === 'month') d.setMonth(d.getMonth() - 1);
                    else if (this.currentView === 'week') d.setDate(d.getDate() - 7);
                    else d.setDate(d.getDate() - 1);
                    this.currentDate = d;
                    this.updateCalendar();
                },

                next() {
                    let d = new Date(this.currentDate);
                    if (this.currentView === 'month') d.setMonth(d.getMonth() + 1);
                    else if (this.currentView === 'week') d.setDate(d.getDate() + 7);
                    else d.setDate(d.getDate() + 1);
                    this.currentDate = d;
                    this.updateCalendar();
                },

                goToToday() { 
                    this.currentDate = new Date(); 
                    this.updateCalendar(); 
                },

                showToast(msg, type = 'success') {
                    this.toast = { show: true, message: msg, type: type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                formatDateShort(dateStr) {
                    if (!dateStr) return '';
                    const datePart = dateStr.split(/[ T]/)[0];
                    const d = new Date(datePart + " 12:00:00");
                    if (isNaN(d.getTime())) return '';
                    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' }).replace('.', '').toUpperCase();
                },

                getEventTypeLabel(eventType) {
                    const labels = {
                        social_event: 'Evento Social',
                        photo_shoot: 'Ensaio Fotográfico',
                        venue_visit: 'Visita ao Espaço',
                    };
                    return labels[eventType] || eventType;
                },

                getDayOfWeekName(date) {
                    if (!date) return '';
                    const d = new Date(date);
                    if (isNaN(d.getTime())) return '';
                    return d.toLocaleString('pt-BR', { weekday: 'long' }).charAt(0).toUpperCase() + d.toLocaleString('pt-BR', { weekday: 'long' }).slice(1);
                },

                getMiniMonthDays(year, month) {
                    const startOfMonth = new Date(year, month, 1);
                    const lastDayOfMonth = new Date(year, month + 1, 0);
                    const startOffset = startOfMonth.getDay();
                    const days = [];
                    const prevMonthLastDay = new Date(year, month, 0).getDate();
                    
                    const today = new Date();
                    const todayIso = today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();

                    for (let i = startOffset - 1; i >= 0; i--) {
                        days.push({ dayNumber: prevMonthLastDay - i, isCurrentMonth: false });
                    }
                    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
                        const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === i;
                        days.push({ dayNumber: i, isCurrentMonth: true, isToday });
                    }
                    while (days.length < 42) {
                        days.push({ dayNumber: days.length - lastDayOfMonth.getDate() - startOffset + 1, isCurrentMonth: false });
                    }
                    return days;
                },

                handleSwipe(endX) {
                    const threshold = 50;
                    if (this.touchStartX - endX > threshold) {
                        this.next();
                    } else if (endX - this.touchStartX > threshold) {
                        this.prev();
                    }
                },

                refreshIcons() { setTimeout(() => lucide.createIcons(), 50); }
            }
        }
    </script>
</body>
</html>
