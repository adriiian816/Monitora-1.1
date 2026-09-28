<x-app-layout>
    <div x-data="{ showDeleteModal: false, deleteActionUrl: '', userName: '' }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pengguna & Hak Akses</h1>
                <p class="text-xs text-slate-500 mt-1">Kelola hierarki akun MONITORA: Admin, PIC, dan Staf OPD</p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna Baru
            </a>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Akun</span>
                    <span class="p-1.5 rounded-lg bg-slate-100 text-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $totalUsers }}</div>
                <span class="text-[11px] text-slate-500">Semua pengguna terdaftar</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-purple-200 bg-purple-50/20 shadow-sm">
                <div class="flex items-center justify-between text-purple-700 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Admin</span>
                    <span class="p-1.5 rounded-lg bg-purple-100 text-purple-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-purple-900">{{ $totalSuperAdmin }}</div>
                <span class="text-[11px] text-purple-700">Kontrol penuh sistem</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-blue-200 bg-blue-50/20 shadow-sm">
                <div class="flex items-center justify-between text-blue-700 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">PIC</span>
                    <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-blue-900">{{ $totalAdmin }}</div>
                <span class="text-[11px] text-blue-700">Tugas & verifikasi</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-sm">
                <div class="flex items-center justify-between text-emerald-700 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Staf OPD</span>
                    <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-emerald-900">{{ $totalOpd }}</div>
                <span class="text-[11px] text-emerald-700">Pengunggah berkas</span>
            </div>
        </div>

        <!-- Search & Filter Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, email, atau nama instansi OPD..." 
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="w-full sm:w-48">
                    <select name="role" class="w-full py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                        <option value="">Semua Peran</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Admin</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>PIC</option>
                        <option value="opd" {{ request('role') === 'opd' ? 'selected' : '' }}>Staf OPD</option>
                    </select>
                </div>

                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-800 transition">
                    Filter Pengguna
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-4">Nama Pengguna</th>
                            <th scope="col" class="px-6 py-4">Kontak Login & WA</th>
                            <th scope="col" class="px-6 py-4">Peran (Role)</th>
                            <th scope="col" class="px-6 py-4">Instansi OPD Terkait</th>
                            <th scope="col" class="px-6 py-4">Terdaftar Sejak</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Name -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        {{ $user->name }}
                                        @if($user->id === Auth::id())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                Anda
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Email & WhatsApp -->
                                <td class="px-6 py-4">
                                    <div class="text-xs font-mono text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-200 inline-block">
                                        {{ $user->email }}
                                    </div>
                                    @if($user->no_hp)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            @php
                                                $waPhone = preg_replace('/[^0-9]/', '', $user->no_hp);
                                                if (str_starts_with($waPhone, '0')) {
                                                    $waPhone = '62'.substr($waPhone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $waPhone }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-semibold hover:underline" 
                                               title="Hubungi via WhatsApp">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                {{ $user->no_hp }}
                                            </a>
                                        </div>
                                    @elseif($user->isOpd())
                                        <div class="text-[10px] text-amber-600 italic mt-0.5">No. WA belum diisi</div>
                                    @endif
                                </td>

                                <!-- Role Badge -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $user->role_badge_classes }}">
                                        {{ $user->role_label }}
                                    </span>
                                </td>

                                <!-- Associated OPD -->
                                <td class="px-6 py-4">
                                    @if($user->isOpd() && $user->opd)
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-mono text-[11px] font-bold border border-blue-100">
                                                {{ $user->opd->kode_opd }}
                                            </span>
                                            <span class="text-xs text-slate-700 font-medium truncate max-w-[200px]" title="{{ $user->opd->nama_opd }}">
                                                {{ $user->opd->nama_opd }}
                                            </span>
                                        </div>
                                    @elseif($user->isOpd() && ! $user->opd)
                                        <span class="text-xs text-rose-500 italic">Belum terhubung ke OPD</span>
                                    @elseif($user->isAdmin())
                                        @if($user->handledOpds->isNotEmpty())
                                            <div class="flex flex-wrap items-center gap-1 max-w-xs">
                                                @foreach($user->handledOpds->take(4) as $hOpd)
                                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-mono text-[11px] font-bold border border-blue-100" title="{{ $hOpd->nama_opd }}">
                                                        {{ $hOpd->kode_opd }}
                                                    </span>
                                                @endforeach
                                                @if($user->handledOpds->count() > 4)
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">
                                                        +{{ $user->handledOpds->count() - 4 }} lagi
                                                    </span>
                                                @endif
                                                <span class="text-[10px] text-slate-500 font-semibold block w-full mt-0.5">
                                                    ({{ $user->handledOpds->count() }} OPD dikelola)
                                                </span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-50 text-amber-800 text-[11px] font-medium border border-amber-200">
                                                ⚠️ Belum ada OPD ditugaskan
                                            </span>
                                        @endif
                                    @elseif($user->isSuperAdmin())
                                        <span class="text-xs text-purple-700 font-medium flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                            Semua OPD (Akses Penuh)
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- Registered At -->
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="p-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition" 
                                           title="Edit Akun Pengguna">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        @if($user->id !== Auth::id())
                                            <button type="button" 
                                                    @click="deleteActionUrl = '{{ route('admin.users.destroy', $user) }}'; userName = '{{ addslashes($user->name) }}'; if ($refs.deleteModalForm) { $refs.deleteModalForm.action = '{{ route('admin.users.destroy', $user) }}'; } showDeleteModal = true;"
                                                    class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" 
                                                    title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    Tidak ada data pengguna yang sesuai pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" 
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showDeleteModal = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center gap-3 text-rose-600 mb-4">
                    <div class="p-3 bg-rose-100 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus Akun</h3>
                </div>

                <p class="text-sm text-slate-600 mb-6">
                    Apakah Anda yakin ingin menghapus akun <strong class="text-slate-900" x-text="userName"></strong>? Akun ini tidak akan dapat login lagi ke dalam sistem MONITORA.
                </p>

                <form x-ref="deleteModalForm" method="POST" :action="deleteActionUrl" class="flex items-center justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            @click="showDeleteModal = false" 
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-rose-600 text-white font-bold text-sm hover:bg-rose-700 shadow-sm transition">
                        Ya, Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
