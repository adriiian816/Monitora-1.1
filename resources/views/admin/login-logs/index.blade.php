<x-app-layout pageTitle="Log Sesi Pengguna">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="flex flex-col w-full max-w-7xl mx-auto gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">history</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Audit &amp; Log Sesi Pengguna</h1>
                        <span class="text-[11px] font-semibold text-purple-800 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-full">
                            Admin Only
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">
                        Audit riwayat login/logout staf OPD, deteksi perangkat, browser, alamat IP, dan histori akses
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Purge Logs Form / Button -->
                <form method="POST" action="{{ route('admin.login-logs.purge') }}" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan rekaman log sesi yang lebih lama dari 30 hari?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="days" value="30">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 shadow-xs transition cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-rose-600">delete_sweep</span>
                        <span>Bersihkan Log >30 Hari</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== ALERT NOTIFICATIONS ===== --}}
        @if(session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0">check_circle</span>
                <div class="mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        {{-- ===== SUMMARY KPI CARDS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Login Hari Ini --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">login</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Login Hari Ini</div>
                    <div class="text-2xl font-black text-[#064e3b] leading-tight">{{ $totalLoginHariIni }}</div>
                    <div class="text-[10px] text-[#059669] font-semibold">Sesi masuk baru</div>
                </div>
            </div>

            {{-- Card 2: Logout Hari Ini --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">logout</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Logout Hari Ini</div>
                    <div class="text-2xl font-black text-amber-700 leading-tight">{{ $totalLogoutHariIni }}</div>
                    <div class="text-[10px] text-amber-700 font-semibold">Sesi keluar sistem</div>
                </div>
            </div>

            {{-- Card 3: OPD Aktif Hari Ini --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">domain</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">OPD Aktif Hari Ini</div>
                    <div class="text-2xl font-black text-teal-800 leading-tight">{{ $totalOpdAktifHariIni }}</div>
                    <div class="text-[10px] text-teal-700 font-semibold">Instansi berinteraksi</div>
                </div>
            </div>

            {{-- Card 4: Perangkat Hari Ini --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">devices</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Perangkat Sesi</div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-lg font-black text-slate-800">💻 {{ $totalDesktopToday }}</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-lg font-black text-teal-700">📱 {{ $totalMobileToday }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">Desktop vs Mobile</div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('admin.login-logs.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col md:flex-row items-stretch gap-2.5 rounded-full">
            
            {{-- Search Text --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama staf, OPD, IP, lokasi..."
                       type="text">
            </div>

            {{-- Filter OPD --}}
            <div class="relative min-w-[170px]">
                <select name="opd_id" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua OPD</option>
                    @foreach($opdList as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->kode_opd }} - {{ Str::limit($opd->nama_opd, 18) }}
                        </option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Filter Action --}}
            <div class="relative min-w-[140px]">
                <select name="action" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Aksi</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Masuk (Login)</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Keluar (Logout)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Filter Device --}}
            <div class="relative min-w-[140px]">
                <select name="device_type" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Device</option>
                    <option value="desktop" {{ request('device_type') == 'desktop' ? 'selected' : '' }}>Desktop / PC</option>
                    <option value="mobile" {{ request('device_type') == 'mobile' ? 'selected' : '' }}>Mobile / HP</option>
                    <option value="tablet" {{ request('device_type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'opd_id', 'action', 'device_type', 'date']))
                    <a href="{{ route('admin.login-logs.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- Status Info Bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $logs->total() }}</strong> rekaman aktivitas</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">Audit Real-time Aktif</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Login</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Logout</span>
            </div>
        </div>

        {{-- ===== TABEL LOG SESI ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#f7faf8] border-b border-emerald-100/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-4">Waktu &amp; Aksi</th>
                            <th scope="col" class="px-6 py-4">Pengguna &amp; OPD</th>
                            <th scope="col" class="px-6 py-4">Perangkat &amp; Browser</th>
                            <th scope="col" class="px-6 py-4">Alamat IP</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-[#f0f8f4] transition-colors group">
                                <!-- Waktu & Aksi -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 block text-xs">
                                        {{ $log->logged_at->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $log->logged_at->format('H:i:s') }} WIT
                                    </span>
                                    <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border
                                        {{ $log->action === 'login' ? 'bg-emerald-50 text-[#059669] border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        @if($log->action === 'login')
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
                                            LOGIN
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            LOGOUT
                                        @endif
                                    </span>
                                </td>

                                <!-- Pengguna & OPD -->
                                <td class="px-6 py-4">
                                    @if($log->user)
                                        <div class="font-bold text-slate-800 text-sm">{{ $log->user->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $log->user->email }}</div>
                                    @else
                                        <div class="font-semibold text-slate-400 italic text-xs">Pengguna Dihapus</div>
                                    @endif

                                    @if($log->opd)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#f0f5f2] text-[#064e3b] border border-emerald-200 font-mono">
                                                {{ $log->opd->kode_opd }}
                                            </span>
                                            <span class="text-xs text-slate-600 truncate max-w-[200px]" title="{{ $log->opd->nama_opd }}">
                                                {{ $log->opd->nama_opd }}
                                            </span>
                                        </div>
                                    @elseif($log->user && $log->user->isAdminOrSuperAdmin())
                                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            {{ $log->user->role_label }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Perangkat & Browser -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        @if($log->device_type === 'mobile')
                                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0" title="Perangkat Ponsel (Mobile)">
                                                <span class="material-symbols-outlined text-[18px]">smartphone</span>
                                            </div>
                                        @elseif($log->device_type === 'tablet')
                                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0" title="Perangkat Tablet">
                                                <span class="material-symbols-outlined text-[18px]">tablet_mac</span>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0" title="Perangkat Komputer (Desktop)">
                                                <span class="material-symbols-outlined text-[18px]">computer</span>
                                            </div>
                                        @endif

                                        <div>
                                            <span class="font-bold text-xs text-slate-800 block">
                                                {{ $log->platform ?: 'OS Tidak Diketahui' }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 block">
                                                {{ $log->browser ?: 'Browser Web' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($log->user_agent)
                                        <div x-data="{ open: false }" class="mt-1">
                                            <button @click="open = !open" type="button" class="text-[10px] text-[#059669] hover:underline font-semibold cursor-pointer">
                                                <span x-show="!open">Detail User-Agent ▾</span>
                                                <span x-show="open">Tutup User-Agent ▴</span>
                                            </button>
                                            <div x-show="open" class="mt-1 p-2 bg-[#f0f5f2] rounded-xl text-[10px] font-mono text-slate-600 break-all max-w-sm border border-emerald-100">
                                                {{ $log->user_agent }}
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                <!-- Alamat IP & Lokasi -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <code class="px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-[#f0f5f2] text-slate-800 border border-emerald-100">
                                            {{ $log->ip_address ?: '-' }}
                                        </code>
                                    </div>
                                </td>

                                <!-- Aksi Hapus Log -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.login-logs.destroy', $log) }}" onsubmit="return confirm('Hapus rekaman log ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition cursor-pointer" title="Hapus log">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-[#f0f5f2] text-slate-400 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[26px]">manage_search</span>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm block">Belum ada riwayat aktivitas sesi</span>
                                    <span class="text-xs text-slate-400 mt-1 block">Aktivitas login dan logout pengguna OPD akan otomatis dicatat di sini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
</x-app-layout>
