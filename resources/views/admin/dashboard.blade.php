<x-app-layout pageTitle="Dashboard Monitoring">
{{-- ===== TOP CONTROL BAR ===== --}}
<div class="px-6 md:px-8 py-4 bg-[#f7faf8] border-b border-emerald-100/70 flex flex-col xl:flex-row xl:items-center justify-between gap-3 shrink-0">
    {{-- Left: Title --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
            <span class="material-symbols-outlined text-[22px]">dashboard</span>
        </div>
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Dashboard Monitoring</h1>
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                    T.A. {{ date('Y') }} • Triwulan I
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status kepatuhan pengumpulan tugas dan berkas OPD secara real-time</p>
        </div>
    </div>

    {{-- Right: Exec Stat Ticker --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1 xl:pb-0">
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Total OPD</span>
                <span class="text-base font-extrabold text-slate-900 leading-tight">{{ $totalOpd }}</span>
            </div>
            <span class="text-[10px] text-[#059669] font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">Mitra Aktif</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Tugas Aktif</span>
                <span class="text-base font-extrabold text-slate-900 leading-tight">{{ $totalTugasAktif }}</span>
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Kegiatan</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Belum Kumpul</span>
                <span class="text-base font-extrabold text-amber-600 leading-tight">{{ $totalBelumKumpul }}</span>
            </div>
            <span class="text-[10px] text-amber-600 font-semibold bg-amber-50 px-1.5 py-0.5 rounded">Menunggu</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Verifikasi</span>
                <span class="text-base font-extrabold text-teal-700 leading-tight">{{ $totalMenungguVerifikasi }}</span>
            </div>
            <span class="text-[10px] text-teal-700 font-semibold bg-teal-50 px-1.5 py-0.5 rounded">Telaah Kabid</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Diterima</span>
                <span class="text-base font-extrabold text-emerald-600 leading-tight">{{ $totalDiterima }}</span>
            </div>
            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">Valid &amp; Final</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-rose-600">Terlambat</span>
                <span class="text-base font-extrabold text-rose-600 leading-tight">{{ $totalTerlambat }}</span>
            </div>
            <span class="text-[10px] text-rose-600 font-bold bg-white px-1.5 py-0.5 rounded">Lewat Deadline</span>
        </div>
        {{-- Quick Actions --}}
        @if(Auth::user()->isAdminOrSuperAdmin())
            <a href="{{ route('admin.tugas.create') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#064e3b] text-white text-[12px] font-bold shadow-sm hover:bg-[#043d2e] transition whitespace-nowrap ml-1">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tugas Baru
            </a>
        @endif
    </div>
</div>

{{-- ===== MAIN SPLIT-PANEL WORKSPACE ===== --}}
<div class="flex-1 flex flex-col lg:flex-row min-w-0">

    {{-- ===== LEFT PANEL: Task Monitoring (65%) ===== --}}
    <section class="flex-1 lg:w-[65%] p-6 md:p-8 flex flex-col gap-4">

        {{-- OPD Warning (PIC tanpa OPD) --}}
        @if($isPic && count($handledOpdIds) === 0)
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
                <span class="material-symbols-outlined text-[20px] text-amber-600 shrink-0 mt-0.5">warning</span>
                <div class="text-xs">
                    <h4 class="font-bold text-sm text-amber-950 mb-0.5">Penugasan OPD Belum Ditetapkan</h4>
                    <p class="leading-relaxed">Akun PIC Anda saat ini belum ditugaskan untuk mengelola perangkat daerah (OPD) manapun. Silakan hubungi Administrator.</p>
                </div>
            </div>
        @endif

        {{-- Workspace Header --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Monitoring Tugas OPD Real-time</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Progres pengumpulan dokumen per tugas aktif</p>
            </div>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="flex items-center gap-1 text-[12px] text-slate-500 bg-[#f0f5f2] rounded-full overflow-hidden border border-emerald-100">
                    <span class="material-symbols-outlined text-[15px] text-slate-400 pl-3">search</span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama tugas..."
                           class="py-1.5 pr-3 pl-1 text-[12px] bg-transparent border-0 focus:ring-0 focus:outline-none w-36">
                    <button type="submit" class="px-3 py-1.5 bg-[#059669] text-white text-[11px] font-bold hover:bg-[#047857] transition">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        {{-- Tasks Table --}}
        <div class="border border-emerald-100 rounded-2xl overflow-hidden bg-white shadow-xs flex-1">
            {{-- Table Header --}}
            <div class="grid grid-cols-12 px-5 py-3 bg-[#f7faf8] text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-emerald-100/70">
                <div class="col-span-5">NAMA TUGAS</div>
                <div class="col-span-3">DEADLINE</div>
                <div class="col-span-2">PROGRES</div>
                <div class="col-span-2 text-right">STATUS</div>
            </div>

            {{-- Task Rows --}}
            <div class="divide-y divide-slate-100">
                @forelse($daftarTugas as $tugas)
                    @php
                        $totalOpdCount = $tugas->total_ditugaskan ?: 1;
                        $pct = round(($tugas->total_terkumpul / $totalOpdCount) * 100);
                        $isOverdue = $tugas->isPastDeadline();
                    @endphp
                    <div class="grid grid-cols-12 items-center px-5 py-3.5 hover:bg-[#f0f8f4] transition-colors group cursor-pointer {{ $isOverdue ? 'border-l-2 border-rose-400' : '' }}">
                        {{-- Name --}}
                        <div class="col-span-5 flex items-center gap-3 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-lg {{ $isOverdue ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-[#059669]' }} flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">description</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <a href="{{ route('admin.tugas.show', $tugas) }}"
                                   class="text-[13px] font-semibold text-slate-800 truncate group-hover:text-[#059669] transition-colors">
                                    {{ $tugas->judul }}
                                </a>
                                <span class="text-[10px] text-slate-400">
                                    TA {{ $tugas->tahun_anggaran }} • {{ $tugas->format_label }}
                                </span>
                            </div>
                        </div>
                        {{-- Deadline --}}
                        <div class="col-span-3 text-[12px] {{ $isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-500' }}">
                            <div>{{ $tugas->deadline->translatedFormat('d M Y') }}</div>
                            <div class="text-[10px] font-bold {{ $isOverdue ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $tugas->sisa_waktu_label }}
                            </div>
                        </div>
                        {{-- Progress --}}
                        <div class="col-span-2">
                            <div class="text-[10px] text-slate-500 mb-1">{{ $tugas->total_terkumpul }}/{{ $tugas->total_ditugaskan }} OPD</div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-500 {{ $isOverdue ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 to-teal-400' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="text-[10px] font-bold text-slate-600 mt-0.5">{{ $pct }}%</div>
                        </div>
                        {{-- Actions --}}
                        <div class="col-span-2 flex items-center justify-end gap-1">
                            @if($tugas->total_terlambat > 0)
                                <span class="text-[10px] font-bold text-rose-600">{{ $tugas->total_terlambat }} Terlambat</span>
                            @endif
                            <a href="{{ route('admin.tugas.show', $tugas) }}"
                               class="w-7 h-7 rounded-full hover:bg-emerald-100 flex items-center justify-center text-slate-400 group-hover:text-[#059669] transition-colors">
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                            @if(Auth::user()->isAdminOrSuperAdmin())
                                <a href="{{ route('admin.tugas.edit', $tugas) }}"
                                   class="w-7 h-7 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 opacity-60 group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-[15px]">more_vert</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-sm">
                        <span class="material-symbols-outlined text-[48px] text-slate-200 block mb-3">inbox</span>
                        Belum ada tugas yang dibuat. Klik <strong class="text-[#059669]">"+ Tugas Baru"</strong> untuk mendistribusikan penugasan ke OPD.
                    </div>
                @endforelse
            </div>

            @if($daftarTugas->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $daftarTugas->links() }}
                </div>
            @endif
        </div>

        {{-- ===== INTERACTIVE ASSIGNMENT CALENDAR ===== --}}
        <div class="bg-white border border-emerald-100/80 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col gap-5"
             x-data="assignmentCalendar({{ json_encode($calendarEvents) }})">

            {{-- Calendar Header & Controls --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#059669] flex items-center justify-center border border-emerald-200 shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-[#064e3b] tracking-tight">Kalender Penugasan &amp; Agenda</h3>
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full"
                                  x-text="filteredEvents.length + ' Agenda Terjadwal'"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Jadwal batas waktu pengumpulan berkas OPD &amp; penugasan staf bidang</p>
                    </div>
                </div>

                {{-- Month Controls & Filters --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Filter Tabs --}}
                    <div class="flex items-center p-0.5 bg-[#f0f5f2] rounded-full border border-emerald-100 text-[11px] font-semibold">
                        <button type="button" 
                                @click="filterType = 'all'"
                                :class="filterType === 'all' ? 'bg-white text-[#064e3b] shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                                class="px-2.5 py-1 rounded-full transition">
                            Semua
                        </button>
                        <button type="button" 
                                @click="filterType = 'tugas_opd'"
                                :class="filterType === 'tugas_opd' ? 'bg-white text-[#059669] shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                                class="flex items-center gap-1 px-2.5 py-1 rounded-full transition">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
                            <span>Tugas OPD</span>
                        </button>
                        <button type="button" 
                                @click="filterType = 'penugasan_bidang'"
                                :class="filterType === 'penugasan_bidang' ? 'bg-white text-indigo-700 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                                class="flex items-center gap-1 px-2.5 py-1 rounded-full transition">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            <span>Staf Bidang</span>
                        </button>
                    </div>

                    {{-- Month Navigator --}}
                    <div class="flex items-center gap-1 bg-[#f0f5f2] rounded-full p-1 border border-emerald-100">
                        <button type="button" 
                                @click="prevMonth()" 
                                class="w-7 h-7 rounded-full hover:bg-white text-slate-600 hover:text-[#064e3b] flex items-center justify-center transition"
                                title="Bulan Sebelumnya">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        <span class="text-xs font-bold text-[#064e3b] px-2 min-w-[110px] text-center" x-text="currentMonthName"></span>
                        <button type="button" 
                                @click="nextMonth()" 
                                class="w-7 h-7 rounded-full hover:bg-white text-slate-600 hover:text-[#064e3b] flex items-center justify-center transition"
                                title="Bulan Berikutnya">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>

                    <button type="button" 
                            @click="goToToday()"
                            class="px-2.5 py-1 rounded-full bg-emerald-50 hover:bg-emerald-100 text-[#059669] border border-emerald-200 text-[11px] font-bold transition">
                        Hari Ini
                    </button>
                </div>
            </div>

            {{-- 2-Column Calendar & Agenda Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                {{-- Left: Calendar Grid (7 Cols on desktop) --}}
                <div class="lg:col-span-7 flex flex-col gap-2">
                    {{-- Weekday Headers --}}
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase">Sen</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase">Sel</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase">Rab</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase">Kam</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase">Jum</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase text-amber-600/80">Sab</span>
                        <span class="text-[10px] font-bold text-slate-400 py-1 uppercase text-rose-500">Min</span>
                    </div>

                    {{-- Calendar Days Grid --}}
                    <div class="grid grid-cols-7 gap-1.5">
                        <template x-for="(d, index) in daysInMonth" :key="index">
                            <button type="button"
                                    @click="selectDate(d.dateStr)"
                                    :class="{
                                        'opacity-35 bg-slate-50/60 text-slate-400': !d.isCurrentMonth,
                                        'bg-[#fcfdfc] text-slate-800 hover:border-[#059669] hover:bg-emerald-50/30': d.isCurrentMonth && d.dateStr !== selectedDateStr,
                                        'bg-emerald-700 text-white shadow-sm ring-2 ring-emerald-600/30 font-bold': d.dateStr === selectedDateStr,
                                        'border-emerald-300': d.isToday && d.dateStr !== selectedDateStr,
                                        'border-slate-200/80': !d.isToday && d.dateStr !== selectedDateStr
                                    }"
                                    class="relative h-14 p-1.5 rounded-xl border flex flex-col justify-between items-start transition-all text-left group">
                                
                                {{-- Date Number & Today Indicator --}}
                                <div class="w-full flex items-center justify-between">
                                    <span class="text-xs font-semibold leading-none"
                                          :class="{'text-white font-extrabold': d.dateStr === selectedDateStr, 'text-emerald-700 font-bold': d.isToday && d.dateStr !== selectedDateStr}"
                                          x-text="d.day"></span>
                                    
                                    <template x-if="d.isToday">
                                        <span class="text-[8px] font-black px-1 rounded uppercase tracking-tighter"
                                              :class="d.dateStr === selectedDateStr ? 'bg-white text-emerald-800' : 'bg-emerald-100 text-[#059669]'">
                                            KINI
                                        </span>
                                    </template>
                                </div>

                                {{-- Event Dots / Badges --}}
                                <div class="w-full flex items-center gap-1 mt-auto flex-wrap">
                                    <template x-for="(ev, evIdx) in d.events.slice(0, 3)" :key="evIdx">
                                        <span class="w-2 h-2 rounded-full ring-1 ring-white/60 shrink-0"
                                              :class="{
                                                  'bg-[#059669]': ev.badge_color === 'emerald',
                                                  'bg-indigo-500': ev.badge_color === 'indigo',
                                                  'bg-amber-500': ev.badge_color === 'amber'
                                              }"
                                              :title="ev.type_label + ': ' + ev.title"></span>
                                    </template>
                                    <template x-if="d.events.length > 3">
                                        <span class="text-[8px] font-bold" 
                                              :class="d.dateStr === selectedDateStr ? 'text-emerald-100' : 'text-slate-500'"
                                              x-text="'+' + (d.events.length - 3)"></span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Calendar Legend --}}
                    <div class="flex items-center gap-4 text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                            <span>Deadline Tugas OPD</span>
                        </div>
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <span>Kegiatan Anggota Bidang</span>
                        </div>
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Batas Laporan Bidang</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Selected Date Agenda Details (5 Cols on desktop) --}}
                <div class="lg:col-span-5 bg-[#f8fbf9] rounded-2xl p-4 border border-emerald-100/70 flex flex-col gap-3">
                    <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Agenda Terjadwal</span>
                            <h4 class="text-xs font-bold text-[#064e3b]" x-text="formatDateIndo(selectedDateStr)"></h4>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full"
                              :class="selectedDateEvents.length > 0 ? 'bg-emerald-100 text-[#064e3b]' : 'bg-slate-100 text-slate-500'"
                              x-text="selectedDateEvents.length + ' Agenda'"></span>
                    </div>

                    {{-- Event List for Selected Date --}}
                    <div class="flex flex-col gap-2.5 max-h-[290px] overflow-y-auto pr-1">
                        <template x-for="event in selectedDateEvents" :key="event.id + '-' + event.type">
                            <div class="p-3 bg-white rounded-xl border border-slate-200/70 shadow-2xs hover:border-[#059669] transition flex flex-col gap-1.5 group">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider"
                                          :class="{
                                              'bg-emerald-50 text-[#059669] border border-emerald-200': event.badge_color === 'emerald',
                                              'bg-indigo-50 text-indigo-700 border border-indigo-200': event.badge_color === 'indigo',
                                              'bg-amber-50 text-amber-700 border border-amber-200': event.badge_color === 'amber'
                                          }"
                                          x-text="event.type_label"></span>
                                    <span class="text-[10px] font-semibold text-slate-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        <span x-text="event.time || 'Sepanjang Hari'"></span>
                                    </span>
                                </div>
                                <div class="text-xs font-bold text-slate-800 group-hover:text-[#059669] transition-colors leading-snug line-clamp-2"
                                     x-text="event.title"></div>
                                <div class="text-[10px] text-slate-500" x-text="event.detail"></div>
                                <div class="pt-1.5 border-t border-slate-100 flex items-center justify-end">
                                    <a :href="event.url" 
                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-[#059669] hover:underline">
                                        <span>Buka Detail</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedDateEvents.length === 0">
                            <div class="py-8 text-center flex flex-col items-center justify-center text-slate-400 gap-2">
                                <span class="material-symbols-outlined text-[36px] text-emerald-200">event_available</span>
                                <div class="text-xs font-semibold text-slate-600">Tidak ada agenda pada tanggal ini</div>
                                <p class="text-[10px] text-slate-400 max-w-[200px]">Pilih tanggal bertanda warna di kalender untuk melihat detail penugasan OPD &amp; staf bidang.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== RIGHT PANEL: Quick Access (35%) ===== --}}
    <aside class="w-full lg:w-[35%] shrink-0 border-t lg:border-t-0 lg:border-l border-emerald-100 bg-[#fcfdfc] p-6 flex flex-col gap-5">

        {{-- Submissions Awaiting Verification --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">pending_actions</span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">MENUNGGU VERIFIKASI</h2>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                    {{ $submissionsMenunggu->count() }} berkas
                </span>
            </div>

            <div class="flex flex-col gap-2">
                @forelse($submissionsMenunggu as $sub)
                    <div class="relative bg-white rounded-2xl p-4 border border-emerald-100 shadow-xs flex items-start justify-between gap-3 hover:-translate-y-0.5 transition-transform">
                        <div class="min-w-0">
                            <span class="text-[11px] font-bold text-[#064e3b] block truncate">
                                {{ $sub->tugasOpd->opd->nama_opd }}
                            </span>
                            <p class="text-xs font-semibold text-slate-900 mt-0.5 line-clamp-1">
                                {{ $sub->tugasOpd->tugas->judul }}
                            </p>
                            <span class="text-[10px] text-slate-500 mt-1 block">
                                Dikumpulkan {{ $sub->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <a href="{{ route('admin.submissions.show', $sub) }}"
                           class="px-3 py-1.5 rounded-lg bg-[#064e3b] hover:bg-[#043d2e] text-white text-xs font-bold shrink-0 shadow-sm transition">
                            Periksa
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <span class="material-symbols-outlined text-[32px] text-emerald-200 block mb-2">check_circle</span>
                        Semua pengumpulan telah diverifikasi!
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent WA Activity --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">chat</span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">AKTIVITAS WHATSAPP</h2>
                </div>
                <a href="{{ route('admin.whatsapp-logs.index') }}" class="text-[12px] text-[#059669] font-semibold hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="flex flex-col gap-2">
                @forelse($recentLogs as $log)
                    <div class="relative bg-[#f0f5f2] hover:bg-[#e6efe9] rounded-2xl p-4 border border-emerald-100/80 hover:-translate-y-0.5 transition-transform">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-slate-800">
                                {{ $log->opd ? $log->opd->kode_opd : 'Broadcast' }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
                                {{ $log->status === 'terkirim' ? 'bg-emerald-100 text-emerald-800' : ($log->status === 'simulasi' ? 'bg-teal-100 text-teal-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </div>
                        <span class="text-[10px] font-bold text-[#064e3b] block">{{ $log->tipe_label }}</span>
                        <p class="text-slate-600 mt-1 line-clamp-2 italic text-[11px]">"{{ Str::limit($log->isi_pesan, 70) }}"</p>
                        <span class="text-[10px] text-slate-400 block mt-1">{{ $log->sent_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-[#f0f5f2] border border-emerald-100">
                        Belum ada aktivitas pengiriman WhatsApp.
                    </div>
                @endforelse
            </div>
        </div>
    </aside>
</div>

<script>
function assignmentCalendar(eventsData) {
    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

    return {
        events: eventsData || [],
        filterType: 'all',
        currentYear: now.getFullYear(),
        currentMonth: now.getMonth(),
        selectedDateStr: todayStr,

        get monthNames() {
            return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        },

        get currentMonthName() {
            return this.monthNames[this.currentMonth] + ' ' + this.currentYear;
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },

        goToToday() {
            const cur = new Date();
            this.currentYear = cur.getFullYear();
            this.currentMonth = cur.getMonth();
            this.selectedDateStr = `${cur.getFullYear()}-${pad(cur.getMonth() + 1)}-${pad(cur.getDate())}`;
        },

        get filteredEvents() {
            if (this.filterType === 'all') return this.events;
            if (this.filterType === 'tugas_opd') return this.events.filter(e => e.type === 'tugas_opd');
            if (this.filterType === 'penugasan_bidang') return this.events.filter(e => e.type.startsWith('penugasan_'));
            return this.events;
        },

        getEventsForDate(dateStr) {
            return this.filteredEvents.filter(e => e.date === dateStr);
        },

        get selectedDateEvents() {
            return this.getEventsForDate(this.selectedDateStr);
        },

        get daysInMonth() {
            const year = this.currentYear;
            const month = this.currentMonth;
            
            const firstDayOfWeek = new Date(year, month, 1).getDay();
            const startingDayOffset = (firstDayOfWeek === 0) ? 6 : firstDayOfWeek - 1;
            
            const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
            const prevMonthTotalDays = new Date(year, month, 0).getDate();
            
            const days = [];
            
            // Prev month padding
            for (let i = startingDayOffset - 1; i >= 0; i--) {
                const d = prevMonthTotalDays - i;
                const prevM = month === 0 ? 11 : month - 1;
                const prevY = month === 0 ? year - 1 : year;
                const dateStr = `${prevY}-${pad(prevM + 1)}-${pad(d)}`;
                days.push({
                    day: d,
                    dateStr: dateStr,
                    isCurrentMonth: false,
                    isToday: dateStr === todayStr,
                    events: this.getEventsForDate(dateStr)
                });
            }
            
            // Current month days
            for (let d = 1; d <= totalDaysInMonth; d++) {
                const dateStr = `${year}-${pad(month + 1)}-${pad(d)}`;
                days.push({
                    day: d,
                    dateStr: dateStr,
                    isCurrentMonth: true,
                    isToday: dateStr === todayStr,
                    events: this.getEventsForDate(dateStr)
                });
            }
            
            // Next month padding
            const totalCells = days.length <= 35 ? 35 : 42;
            const remaining = totalCells - days.length;
            for (let d = 1; d <= remaining; d++) {
                const nextM = month === 11 ? 0 : month + 1;
                const nextY = month === 11 ? year + 1 : year;
                const dateStr = `${nextY}-${pad(nextM + 1)}-${pad(d)}`;
                days.push({
                    day: d,
                    dateStr: dateStr,
                    isCurrentMonth: false,
                    isToday: dateStr === todayStr,
                    events: this.getEventsForDate(dateStr)
                });
            }
            
            return days;
        },

        selectDate(dateStr) {
            this.selectedDateStr = dateStr;
        },

        formatDateIndo(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parts[0];
            const m = parseInt(parts[1], 10);
            const d = parseInt(parts[2], 10);
            return `${d} ${this.monthNames[m - 1]} ${y}`;
        }
    };
}
</script>
</x-app-layout>
