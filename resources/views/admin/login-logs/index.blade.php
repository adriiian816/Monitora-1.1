<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Audit & Log Sesi Pengguna OPD</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200 uppercase tracking-wider">
                    Admin Only
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Audit riwayat login dan logout seluruh staf OPD dan pengguna sistem, termasuk deteksi jenis perangkat, browser, alamat IP, dan perkiraan lokasi geografis.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Purge Logs Form / Button -->
            <form method="POST" action="{{ route('admin.login-logs.purge') }}" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan rekaman log sesi yang lebih lama dari 30 hari?');">
                @csrf
                @method('DELETE')
                <input type="hidden" name="days" value="30">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Bersihkan Log >30 Hari
                </button>
            </form>
        </div>
    </div>

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-900 text-sm font-medium shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Login Hari Ini</span>
                <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $totalLoginHariIni }}</span>
                <span class="text-[11px] text-slate-400">Sesi masuk baru</span>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Logout Hari Ini</span>
                <span class="text-2xl font-black text-amber-700 mt-1 block">{{ $totalLogoutHariIni }}</span>
                <span class="text-[11px] text-slate-400">Sesi keluar sistem</span>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-700 font-bold text-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">OPD Aktif Hari Ini</span>
                <span class="text-2xl font-black text-blue-800 mt-1 block">{{ $totalOpdAktifHariIni }}</span>
                <span class="text-[11px] text-slate-400">Instansi berinteraksi</span>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 text-blue-700 font-bold text-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Perangkat Hari Ini</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-lg font-black text-slate-800">💻 {{ $totalDesktopToday }}</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-lg font-black text-sky-700">📱 {{ $totalMobileToday }}</span>
                </div>
                <span class="text-[11px] text-slate-400">Desktop vs Mobile</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.login-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama staf, OPD, IP, lokasi..." 
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Filter OPD -->
            <div>
                <select name="opd_id" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua OPD</option>
                    @foreach($opdList as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->kode_opd }} - {{ Str::limit($opd->nama_opd, 22) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Action -->
            <div>
                <select name="action" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua Aksi</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>🟢 Masuk (Login)</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>🟠 Keluar (Logout)</option>
                </select>
            </div>

            <!-- Filter Device -->
            <div>
                <select name="device_type" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua Perangkat</option>
                    <option value="desktop" {{ request('device_type') == 'desktop' ? 'selected' : '' }}>💻 Desktop / PC</option>
                    <option value="mobile" {{ request('device_type') == 'mobile' ? 'selected' : '' }}>📱 Mobile / HP</option>
                    <option value="tablet" {{ request('device_type') == 'tablet' ? 'selected' : '' }}>📟 Tablet</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-950 transition text-center shadow-sm">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'opd_id', 'action', 'device_type', 'date']))
                    <a href="{{ route('admin.login-logs.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <tr>
                        <th scope="col" class="px-6 py-4">Waktu & Aksi</th>
                        <th scope="col" class="px-6 py-4">Pengguna & OPD</th>
                        <th scope="col" class="px-6 py-4">Perangkat & Browser</th>
                        <th scope="col" class="px-6 py-4">Alamat IP</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Waktu & Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block text-xs">
                                    {{ $log->logged_at->translatedFormat('d F Y') }}
                                </span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    {{ $log->logged_at->format('H:i:s') }} WIT
                                </span>
                                <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $log->action_badge_classes }}">
                                    @if($log->action === 'login')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
                                    <div class="font-semibold text-slate-900">{{ $log->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $log->user->email }}</div>
                                @else
                                    <div class="font-semibold text-slate-500 italic">Pengguna Dihapus</div>
                                @endif

                                @if($log->opd)
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $log->opd->kode_opd }}
                                        </span>
                                        <span class="text-xs text-slate-600 truncate max-w-[200px]" title="{{ $log->opd->nama_opd }}">
                                            {{ $log->opd->nama_opd }}
                                        </span>
                                    </div>
                                @elseif($log->user && $log->user->isAdminOrSuperAdmin())
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $log->user->role_label }}
                                    </span>
                                @endif
                            </td>

                            <!-- Perangkat & Browser -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($log->device_type === 'mobile')
                                        <span class="p-1.5 rounded-lg bg-sky-50 text-sky-700" title="Perangkat Ponsel (Mobile)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </span>
                                    @elseif($log->device_type === 'tablet')
                                        <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-700" title="Perangkat Tablet">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </span>
                                    @else
                                        <span class="p-1.5 rounded-lg bg-slate-100 text-slate-700" title="Perangkat Komputer (Desktop)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </span>
                                    @endif

                                    <div>
                                        <span class="font-bold text-xs text-slate-800 block">
                                            {{ $log->platform ?: 'OS Tidak Diketahui' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 block">
                                            {{ $log->browser ?: 'Browser Web' }}
                                        </span>
                                    </div>
                                </div>

                                @if($log->user_agent)
                                    <div x-data="{ open: false }" class="mt-1">
                                        <button @click="open = !open" type="button" class="text-[10px] text-blue-600 hover:underline">
                                            <span x-show="!open">Detail User-Agent ▾</span>
                                            <span x-show="open">Tutup User-Agent ▴</span>
                                        </button>
                                        <div x-show="open" class="mt-1 p-2 bg-slate-100 rounded text-[10px] font-mono text-slate-600 break-all max-w-sm">
                                            {{ $log->user_agent }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Alamat IP & Lokasi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <code class="px-2 py-0.5 rounded text-xs font-mono font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $log->ip_address ?: '-' }}
                                    </code>
                                </div>


                            </td>

                            <!-- Aksi Hapus Log -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.login-logs.destroy', $log) }}" onsubmit="return confirm('Hapus rekaman log ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus log">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="font-bold text-slate-600 text-sm">Belum ada riwayat aktivitas sesi</span>
                                    <span class="text-xs text-slate-400 mt-1">Aktivitas login dan logout pengguna OPD akan otomatis dicatat di sini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
