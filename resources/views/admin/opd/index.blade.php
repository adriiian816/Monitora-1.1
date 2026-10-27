<x-app-layout pageTitle="Data OPD">
    @php
        $availableOpdIds = $opds->pluck('id')->values();
    @endphp

<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]"
      x-data="{ 
        selectedOpds: [], 
        availableOpdIds: {{ json_encode($availableOpdIds) }},
        opdList: {{ json_encode($opds->items()) }},
        showDeleteModal: false, 
        showCreateOpdModal: false,
        showEditOpdModal: false,
        editOpd: { id: '', kode_opd: '', nama_opd: '', alamat: '', is_active: 1 },

        selectAll() {
            this.selectedOpds = [...this.availableOpdIds];
        },

        deselectAll() {
            this.selectedOpds = [];
        },

        toggleSelectAll(e) {
            if (e.target.checked) {
                this.selectAll();
            } else {
                this.deselectAll();
            }
        },

        isAllSelected() {
            return this.availableOpdIds.length > 0 && this.availableOpdIds.every(id => this.selectedOpds.map(Number).includes(Number(id)));
        },

        openCreateOpdModal() {
            this.showCreateOpdModal = true;
        },

        openEditOpdModal(opd) {
            this.editOpd = {
                id: opd.id,
                kode_opd: opd.kode_opd,
                nama_opd: opd.nama_opd,
                alamat: opd.alamat || '',
                is_active: opd.is_active ? 1 : 0
            };
            this.showEditOpdModal = true;
        },

        handleEdit() {
            let targetId = null;
            if (this.selectedOpds.length > 0) {
                targetId = this.selectedOpds[0];
            } else if (this.opdList.length > 0) {
                targetId = this.opdList[0].id;
            }

            if (targetId) {
                const found = this.opdList.find(o => Number(o.id) === Number(targetId));
                if (found) {
                    this.openEditOpdModal(found);
                }
            } else {
                alert('Tidak ada data OPD yang dapat diedit.');
            }
        },

        selectOpdToEdit(id) {
            const found = this.opdList.find(o => Number(o.id) === Number(id));
            if (found) {
                this.openEditOpdModal(found);
            }
        },

        handleDelete() {
            this.showDeleteModal = true;
        }
    }">
    <div class="flex flex-col w-full max-w-7xl mx-auto gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">domain</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Master Data Instansi OPD</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            Pemerintah Provinsi Maluku Utara
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Kelola direktori Organisasi Perangkat Daerah, pemetaan akun staf &amp; PIC, serta rekapitulasi tugas</p>
                </div>
            </div>
            
            {{-- Header Action Buttons --}}
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Edit Data -->
                <button type="button" 
                        @click="handleEdit()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-slate-600">edit</span>
                    <span>Edit Data</span>
                    <span x-show="selectedOpds.length > 0" 
                          x-text="'(' + selectedOpds.length + ')'" 
                          class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-[#064e3b] font-extrabold"></span>
                </button>

                <!-- Tombol Hapus Data -->
                <button type="button" 
                        @click="handleDelete()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 shadow-xs transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-rose-600">delete</span>
                    <span>Hapus Data</span>
                    <span x-show="selectedOpds.length > 0" 
                          x-text="'(' + selectedOpds.length + ')'" 
                          class="px-2 py-0.5 rounded-full text-[10px] bg-rose-200 text-rose-800 font-extrabold"></span>
                </button>

                <!-- Tambah OPD Baru (Popup Modal) -->
                <button type="button" 
                        @click="openCreateOpdModal()" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] shrink-0 self-start sm:self-auto rounded-full cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>+ Tambah OPD Baru</span>
                </button>
            </div>
        </div>

        {{-- ===== ALERT NOTIFICATIONS ===== --}}
        @if(session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0">check_circle</span>
                <div class="mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-rose-600 text-[20px] shrink-0">error</span>
                <div class="mt-0.5">{{ session('error') }}</div>
            </div>
        @endif

        {{-- ===== SUMMARY KPI CARDS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Instansi --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-5 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Instansi</div>
                    <div class="text-2xl font-black text-slate-900 leading-tight">{{ $totalOpds }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">Semua OPD terdaftar</div>
                </div>
            </div>

            {{-- Card 2: OPD Aktif --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-5 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">OPD Aktif</div>
                    <div class="text-2xl font-black text-emerald-700 leading-tight">{{ $totalActiveOpds }}</div>
                    <div class="text-[10px] text-[#059669] font-semibold">Operasional aktif</div>
                </div>
            </div>

            {{-- Card 3: Staf OPD --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-5 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">group</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akun Staf OPD</div>
                    <div class="text-2xl font-black text-teal-700 leading-tight">{{ $totalStaff }}</div>
                    <div class="text-[10px] text-teal-700 font-semibold">Pengunggah berkas</div>
                </div>
            </div>

            {{-- Card 4: Total Tugas Masuk --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-5 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">folder_special</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Tugas OPD</div>
                    <div class="text-2xl font-black text-amber-700 leading-tight">{{ $totalTugas }}</div>
                    <div class="text-[10px] text-amber-700 font-semibold">Distribusi penugasan</div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('admin.opd.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col sm:flex-row items-stretch gap-2.5 rounded-full">
            
            {{-- Search Input --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama instansi OPD atau kode (mis: DISPERINDAG, DISTAN, DKP)..."
                       type="text">
            </div>

            {{-- Filter & Reset Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter Data</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.opd.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- Banner Pemilihan Aktif (Muncul otomatis saat data dipilih) --}}
        <div x-show="selectedOpds.length > 0" 
             x-transition 
             class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5 text-xs text-[#064e3b] font-bold">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#059669]"></span>
                </span>
                <span x-text="selectedOpds.length + ' data instansi OPD dipilih (dari ' + availableOpdIds.length + ' data di halaman ini)'"></span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" 
                        @click="handleEdit()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-emerald-200 hover:bg-emerald-50 text-[#064e3b] text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-[#059669]">edit</span>
                    <span>Edit Data Terpilih</span>
                </button>
                <button type="button" 
                        @click="handleDelete()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Hapus Data Terpilih</span>
                </button>
                <button type="button" 
                        @click="selectedOpds = []" 
                        class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-800 font-semibold cursor-pointer">
                    Batal Pilih
                </button>
            </div>
        </div>

        {{-- Status Info Bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $opds->total() }}</strong> instansi OPD</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">Sinkronisasi Database Aktif</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-400"></span> Nonaktif</span>
            </div>
        </div>

        {{-- ===== TABEL MASTER DATA OPD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#f7faf8] border-b border-emerald-100/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <!-- Checkbox Pilih Semua -->
                            <th scope="col" class="w-12 px-4 py-4 text-center whitespace-nowrap">
                                <input type="checkbox" 
                                       @change="toggleSelectAll($event)" 
                                       :checked="isAllSelected()"
                                       class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] cursor-pointer"
                                       title="Pilih Semua Data OPD">
                            </th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Kode &amp; Nama Instansi OPD</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Staf OPD &amp; Kontak WhatsApp</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Statistik Tugas</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($opds as $opd)
                            <tr class="hover:bg-[#f0f8f4] transition-colors group"
                                :class="selectedOpds.map(Number).includes({{ $opd->id }}) ? 'bg-emerald-50/40' : ''">
                                
                                <!-- Checkbox Baris -->
                                <td class="w-12 px-4 py-4 text-center whitespace-nowrap">
                                    <input type="checkbox" 
                                           :value="{{ $opd->id }}" 
                                           x-model="selectedOpds" 
                                           class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] cursor-pointer">
                                </td>

                                <!-- OPD Info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#064e3b] font-black text-xs flex items-center justify-center shrink-0 border border-emerald-200 shadow-xs mt-0.5">
                                            {{ substr($opd->kode_opd, 0, 3) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="px-2.5 py-0.5 rounded-full bg-[#f0f5f2] text-[#064e3b] font-mono text-[11px] border border-emerald-200 font-bold">
                                                    {{ $opd->kode_opd }}
                                                </span>
                                                <button type="button" 
                                                        @click="openEditOpdModal({{ json_encode($opd->only(['id', 'kode_opd', 'nama_opd', 'alamat', 'is_active'])) }})" 
                                                        class="font-bold text-slate-800 text-sm group-hover:text-[#059669] hover:underline transition text-left cursor-pointer" 
                                                        title="Klik untuk mengedit data OPD ini">
                                                    {{ $opd->nama_opd }}
                                                </button>
                                            </div>
                                            @if($opd->alamat)
                                                <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-1 truncate max-w-md" title="{{ $opd->alamat }}">
                                                    <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">location_on</span>
                                                    <span>{{ $opd->alamat }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Staf OPD & Kontak WA -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($opd->users->count() > 0)
                                        <div class="space-y-1.5">
                                            @foreach($opd->users->take(2) as $staff)
                                                <div class="text-xs flex items-center gap-2 flex-wrap">
                                                    <span class="font-semibold text-slate-900">{{ $staff->name }}</span>
                                                    @if($staff->no_hp)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $staff->no_hp);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62'.substr($cleanPhone, 1);
                                                            }
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 text-[11px] font-mono text-[#059669] bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs" 
                                                           title="Hubungi staf via WhatsApp">
                                                            <span class="material-symbols-outlined text-[12px]">chat</span>
                                                            <span>{{ $staff->no_hp }}</span>
                                                        </a>
                                                    @else
                                                        <span class="text-[10px] text-slate-400 italic">tanpa WA</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($opd->users->count() > 2)
                                                <span class="text-[10px] text-[#059669] font-bold block">+{{ $opd->users->count() - 2 }} staf lainnya</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200 italic">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Belum ada akun staf
                                        </span>
                                    @endif
                                </td>

                                <!-- Statistik Tugas -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5 flex-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#f0f5f2] text-slate-700" title="Total Tugas Ditugaskan">
                                            {{ $opd->total_tugas }} tugas
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200" title="Tugas Selesai / Diterima">
                                            {{ $opd->total_selesai }} selesai
                                        </span>
                                        @if($opd->total_terlambat > 0)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Tugas Terlambat">
                                                • {{ $opd->total_terlambat }} telat
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($opd->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                        <button type="button" 
                                                @click="openEditOpdModal({{ json_encode($opd->only(['id', 'kode_opd', 'nama_opd', 'alamat', 'is_active'])) }})"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#059669] border border-emerald-200 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                title="Edit data OPD ini">
                                            <span class="material-symbols-outlined text-[15px]">edit</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-[#f0f5f2] text-slate-400 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[26px]">domain_disabled</span>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Tidak ada data instansi OPD yang sesuai pencarian.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($opds->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $opds->links() }}
                </div>
            @endif
        </div>

        {{-- ===== MODAL 1: Konfirmasi Hapus Data OPD ===== --}}
        <div x-show="showDeleteModal" 
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showDeleteModal = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200">
                <div class="flex items-center gap-3 text-rose-600 mb-4">
                    <div class="p-3 bg-rose-100 rounded-2xl">
                        <span class="material-symbols-outlined text-rose-600 text-[26px]">warning</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus Data OPD</h3>
                        <p class="text-xs text-slate-500">Tindakan ini menghapus data instansi &amp; akun terkait</p>
                    </div>
                </div>

                {{-- Jika ada data yang dipilih --}}
                <template x-if="selectedOpds.length > 0">
                    <div>
                        <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                            Apakah Anda yakin ingin menghapus <strong class="text-rose-700 font-black" x-text="selectedOpds.length + ' data instansi OPD'"></strong> yang dipilih? Seluruh akun staf dan data berkas penugasan terkait juga akan terhapus.
                        </p>
                        <div class="mb-4 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Ingin hapus seluruh instansi?</span>
                            <button type="button" 
                                    @click="selectAll()" 
                                    x-show="!isAllSelected()" 
                                    class="text-[#059669] hover:underline font-bold cursor-pointer">
                                Pilih Seluruh Data ({{ count($availableOpdIds) }} OPD)
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Jika belum ada data yang dipilih --}}
                <template x-if="selectedOpds.length === 0">
                    <div class="mb-6">
                        <p class="text-sm text-slate-600 mb-3 leading-relaxed">
                            Belum ada instansi yang dicentang. Anda dapat memilih untuk <strong class="text-rose-700 font-bold">menghapus seluruh data instansi OPD</strong> di halaman ini ({{ count($availableOpdIds) }} OPD) sekaligus, atau menutup jendela ini untuk memilih secara manual.
                        </p>
                        <button type="button" 
                                @click="selectAll()" 
                                class="w-full py-2.5 px-4 rounded-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 border border-emerald-200 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px] text-[#059669]">check_circle</span>
                            <span>Centang Seluruh Data OPD ({{ count($availableOpdIds) }} OPD)</span>
                        </button>
                    </div>
                </template>

                <form method="POST" action="{{ route('admin.opd.bulk-delete') }}" class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-slate-100">
                    @csrf
                    <!-- Hidden inputs of selected OPD IDs -->
                    <template x-for="opdId in selectedOpds" :key="opdId">
                        <input type="hidden" name="opd_ids[]" :value="opdId">
                    </template>

                    <button type="button" 
                            @click="showDeleteModal = false" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="selectedOpds.length === 0"
                            :class="selectedOpds.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-rose-600 hover:bg-rose-700 cursor-pointer'"
                            class="px-6 py-2.5 rounded-full text-white font-bold text-xs shadow-sm transition">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== MODAL CREATE OPD (Popup Tambah OPD Baru) ===== --}}
        <div x-show="showCreateOpdModal" 
             x-cloak
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div @click.away="showCreateOpdModal = false" 
                 class="bg-white rounded-[28px] max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-emerald-100 relative">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-3 pb-4 border-b border-emerald-100/70 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">domain_add</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Tambah Instansi OPD Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan instansi OPD Provinsi Maluku Utara</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="showCreateOpdModal = false" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Modal Form -->
                <form method="POST" action="{{ route('admin.opd.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="modal_create_kode_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kode OPD <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_create_kode_opd" 
                               name="kode_opd" 
                               required 
                               placeholder="Contoh: DISPERINDAG" 
                               class="w-full text-sm font-mono uppercase rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                        <span class="text-[11px] text-slate-400 mt-1 block">Kode singkat unik instansi (BPKAD, DKP, DISPAR)</span>
                    </div>

                    <div>
                        <label for="modal_create_nama_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Instansi OPD <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_create_nama_opd" 
                               name="nama_opd" 
                               required 
                               placeholder="Contoh: Dinas Perindustrian dan Perdagangan" 
                               class="w-full text-sm font-semibold rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                    </div>

                    <div>
                        <label for="modal_create_alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alamat Kantor
                        </label>
                        <textarea id="modal_create_alamat" 
                                  name="alamat" 
                                  rows="2" 
                                  placeholder="Contoh: Jl. Trans Halmahera, Gosale, Sofifi" 
                                  class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed"></textarea>
                    </div>

                    <div class="pt-1">
                        <label class="inline-flex items-center cursor-pointer gap-2.5">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] w-4 h-4">
                            <span class="text-xs font-bold text-slate-700">Status OPD Aktif &amp; Operasional</span>
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" 
                                @click="showCreateOpdModal = false" 
                                class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Simpan Data OPD</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== MODAL EDIT OPD (Popup Edit Data OPD) ===== --}}
        <div x-show="showEditOpdModal" 
             x-cloak
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div @click.away="showEditOpdModal = false" 
                 class="bg-white rounded-[28px] max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-emerald-100 relative">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-3 pb-4 border-b border-emerald-100/70 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">edit_document</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Edit Data Instansi OPD</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi dan status instansi OPD</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="showEditOpdModal = false" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Switch OPD Selector (if multiple available) -->
                <div class="mb-4" x-show="opdList.length > 1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        Pilih Instansi Lain Untuk Diedit:
                    </label>
                    <select @change="selectOpdToEdit($event.target.value)" 
                            :value="editOpd.id"
                            class="w-full text-xs rounded-xl border border-slate-200 bg-[#f0f5f2] px-3 py-2 font-medium">
                        <template x-for="item in opdList" :key="item.id">
                            <option :value="item.id" :selected="Number(item.id) === Number(editOpd.id)" x-text="'[' + item.kode_opd + '] ' + item.nama_opd"></option>
                        </template>
                    </select>
                </div>

                <!-- Modal Form -->
                <form method="POST" :action="'/admin/opd/' + editOpd.id" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="modal_edit_kode_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kode OPD <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_edit_kode_opd" 
                               name="kode_opd" 
                               x-model="editOpd.kode_opd"
                               required 
                               class="w-full text-sm font-mono uppercase rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                    </div>

                    <div>
                        <label for="modal_edit_nama_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Instansi OPD <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_edit_nama_opd" 
                               name="nama_opd" 
                               x-model="editOpd.nama_opd"
                               required 
                               class="w-full text-sm font-semibold rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                    </div>

                    <div>
                        <label for="modal_edit_alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alamat Kantor
                        </label>
                        <textarea id="modal_edit_alamat" 
                                  name="alamat" 
                                  x-model="editOpd.alamat"
                                  rows="2" 
                                  class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed"></textarea>
                    </div>

                    <div class="pt-1">
                        <label class="inline-flex items-center cursor-pointer gap-2.5">
                            <input type="checkbox" name="is_active" value="1" x-model="editOpd.is_active" class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] w-4 h-4">
                            <span class="text-xs font-bold text-slate-700">Status OPD Aktif &amp; Operasional</span>
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" 
                                @click="showEditOpdModal = false" 
                                class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Perbarui Data OPD</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</x-app-layout>
