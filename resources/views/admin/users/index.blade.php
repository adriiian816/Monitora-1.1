<x-app-layout pageTitle="Kelola Pengguna">
    @php
        $selectableUserIds = $users->pluck('id')->reject(fn ($id) => (int) $id === Auth::id())->values();
    @endphp

<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]"
      x-data="{ 
        selectedUsers: [], 
        availableUserIds: {{ json_encode($selectableUserIds) }},
        userList: {{ json_encode($users->items()) }},
        showDeleteModal: false, 
        showCreateUserModal: false,
        showEditUserModal: false,
        newUserRole: 'admin',
        editUser: { id: '', name: '', email: '', no_hp: '', role: 'admin', opd_id: '', bidang: '', handled_opd_ids: [] },

        selectAll() {
            this.selectedUsers = [...this.availableUserIds];
        },

        deselectAll() {
            this.selectedUsers = [];
        },

        toggleSelectAll(e) {
            if (e.target.checked) {
                this.selectAll();
            } else {
                this.deselectAll();
            }
        },

        isAllSelected() {
            return this.availableUserIds.length > 0 && this.availableUserIds.every(id => this.selectedUsers.map(Number).includes(Number(id)));
        },

        openCreateUserModal() {
            this.showCreateUserModal = true;
        },

        openEditUserModal(user) {
            this.editUser = {
                id: user.id,
                name: user.name,
                email: user.email,
                no_hp: user.no_hp || '',
                role: user.role,
                opd_id: user.opd_id || '',
                bidang: user.bidang || '',
                handled_opd_ids: (user.handled_opds || []).map(o => o.id)
            };
            this.showEditUserModal = true;
        },

        handleEdit() {
            let targetId = null;
            if (this.selectedUsers.length > 0) {
                targetId = this.selectedUsers[0];
            } else if (this.userList.length > 0) {
                targetId = this.userList[0].id;
            }

            if (targetId) {
                const found = this.userList.find(u => Number(u.id) === Number(targetId));
                if (found) {
                    this.openEditUserModal(found);
                }
            } else {
                alert('Tidak ada data akun yang dapat diedit.');
            }
        },

        selectUserToEdit(id) {
            const found = this.userList.find(u => Number(u.id) === Number(id));
            if (found) {
                this.openEditUserModal(found);
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
                    <span class="material-symbols-outlined text-[22px]">manage_accounts</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Manajemen Pengguna &amp; Hak Akses</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            Hierarki Akun MONITORA
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Kelola akun Admin Bidang Perekonomian &amp; Perdagangan Bappeda, PIC pengelola OPD, serta narahubung staf OPD se-Maluku Utara</p>
                </div>
            </div>
            
            {{-- Header Actions --}}
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Edit Data -->
                <button type="button" 
                        @click="handleEdit()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-slate-600">edit</span>
                    <span>Edit Data</span>
                    <span x-show="selectedUsers.length > 0" 
                          x-text="'(' + selectedUsers.length + ')'" 
                          class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-[#064e3b] font-extrabold"></span>
                </button>

                <!-- Tombol Hapus Data -->
                <button type="button" 
                        @click="handleDelete()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 shadow-xs transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-rose-600">delete</span>
                    <span>Hapus Data</span>
                    <span x-show="selectedUsers.length > 0" 
                          x-text="'(' + selectedUsers.length + ')'" 
                          class="px-2 py-0.5 rounded-full text-[10px] bg-rose-200 text-rose-800 font-extrabold"></span>
                </button>

                <!-- Tambah Pengguna Baru (Popup Modal) -->
                <button type="button" 
                        @click="openCreateUserModal()" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] shrink-0 self-start sm:self-auto rounded-full cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>+ Tambah Pengguna</span>
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
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            {{-- Card 1: Total Akun --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-4 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Akun</div>
                    <div class="text-xl font-black text-slate-900 leading-tight">{{ $totalUsers }}</div>
                    <div class="text-[9px] text-slate-400 font-medium">Semua pengguna</div>
                </div>
            </div>

            {{-- Card 2: Super Admin --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-4 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">shield_person</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Super Admin</div>
                    <div class="text-xl font-black text-purple-900 leading-tight">{{ $totalSuperAdmin }}</div>
                    <div class="text-[9px] text-purple-700 font-semibold">Kontrol sistem</div>
                </div>
            </div>

            {{-- Card 3: PIC --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-4 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">assignment_ind</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">PIC Bappeda</div>
                    <div class="text-xl font-black text-teal-800 leading-tight">{{ $totalAdmin }}</div>
                    <div class="text-[9px] text-teal-700 font-semibold">Tugas &amp; verifikasi</div>
                </div>
            </div>

            {{-- Card 4: Anggota Bidang --}}
            <div class="bg-gradient-to-br from-emerald-50/70 to-white p-4 rounded-3xl border border-emerald-200 shadow-xs hover:shadow-md transition-all flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anggota Bidang</div>
                    <div class="text-xl font-black text-indigo-900 leading-tight">{{ $totalAnggotaBidang }}</div>
                    <div class="text-[9px] text-indigo-700 font-semibold">Staf internal</div>
                </div>
            </div>

            {{-- Card 5: Staf OPD --}}
            <div class="bg-white p-4 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">domain</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Staf OPD</div>
                    <div class="text-xl font-black text-[#064e3b] leading-tight">{{ $totalOpd }}</div>
                    <div class="text-[9px] text-[#059669] font-semibold">Mitra instansi</div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col sm:flex-row items-stretch gap-2.5 rounded-full">
            
            {{-- Search Input --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau nama instansi OPD..."
                       type="text">
            </div>

            {{-- Role Filter --}}
            <div class="relative min-w-[190px]">
                <select name="role" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Peran (Role)</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>PIC Bappeda</option>
                    <option value="anggota_bidang" {{ request('role') === 'anggota_bidang' ? 'selected' : '' }}>Anggota Bidang</option>
                    <option value="opd" {{ request('role') === 'opd' ? 'selected' : '' }}>Staf OPD</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- Banner Pemilihan Aktif --}}
        <div x-show="selectedUsers.length > 0" 
             x-transition 
             class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5 text-xs text-[#064e3b] font-bold">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#059669]"></span>
                </span>
                <span x-text="selectedUsers.length + ' data akun dipilih (dari ' + availableUserIds.length + ' data di halaman ini)'"></span>
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
                        @click="selectedUsers = []" 
                        class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-800 font-semibold cursor-pointer">
                    Batal Pilih
                </button>
            </div>
        </div>

        {{-- Status Info Bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $users->total() }}</strong> pengguna terdaftar</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">Sinkronisasi Autentikasi Aktif</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Super Admin</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-500"></span> PIC</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Staf OPD</span>
            </div>
        </div>

        {{-- ===== TABEL PENGGUNA ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-200 shadow-xs overflow-hidden border-t-4 border-t-[#059669]">
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
                                       title="Pilih Semua Data">
                            </th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Pengguna</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Kontak Login &amp; WA</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Peran (Role)</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Instansi OPD Terkait</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Terdaftar Sejak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            @php
                                $isSelf = $user->id === Auth::id();
                            @endphp
                            <tr class="hover:bg-[#f0f8f4] transition-colors group"
                                :class="selectedUsers.map(Number).includes({{ $user->id }}) ? 'bg-emerald-50/40' : ''">
                                
                                <!-- Checkbox Pemilihan Baris -->
                                <td class="w-12 px-4 py-4 text-center whitespace-nowrap">
                                    @if(! $isSelf)
                                        <input type="checkbox" 
                                               :value="{{ $user->id }}" 
                                               x-model="selectedUsers" 
                                               class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] cursor-pointer">
                                    @else
                                        <span class="text-slate-300 text-xs font-bold" title="Akun Anda yang sedang aktif tidak dapat dipilih untuk dihapus">-</span>
                                    @endif
                                </td>

                                <!-- Name with Avatar Circle -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-xs shrink-0 shadow-xs border
                                            {{ $user->isSuperAdmin() ? 'bg-purple-50 text-purple-700 border-purple-200' : ($user->isAdmin() ? 'bg-teal-50 text-teal-800 border-teal-200' : 'bg-emerald-50 text-[#064e3b] border-emerald-200') }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                <button type="button" 
                                                        @click="openEditUserModal({{ json_encode($user->loadMissing('handledOpds')->only(['id', 'name', 'email', 'no_hp', 'role', 'opd_id', 'bidang', 'handled_opds'])) }})" 
                                                        class="truncate max-w-[200px] sm:max-w-xs group-hover:text-[#059669] hover:underline transition text-left font-bold cursor-pointer" 
                                                        title="Klik untuk mengedit akun: {{ $user->name }}">
                                                    {{ $user->name }}
                                                </button>
                                                @if($isSelf)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shrink-0">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">ID: #{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email & WhatsApp -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        <div class="inline-flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                            <span class="material-symbols-outlined text-[14px] text-slate-400">mail</span>
                                            <span class="font-mono text-[11px]">{{ $user->email }}</span>
                                        </div>
                                        @if($user->no_hp)
                                            @php
                                                $waPhone = preg_replace('/[^0-9]/', '', $user->no_hp);
                                                if (str_starts_with($waPhone, '0')) {
                                                    $waPhone = '62'.substr($waPhone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $waPhone }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#059669] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-2xs" 
                                               title="Hubungi via WhatsApp">
                                                <span class="material-symbols-outlined text-[12px]">chat</span>
                                                <span class="font-mono">{{ $user->no_hp }}</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 italic">
                                                No. WA belum diisi
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Role & Bidang Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        @if($user->isSuperAdmin())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                {{ $user->role_label }}
                                            </span>
                                        @elseif($user->isAdmin())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                                                {{ $user->role_label }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
                                                {{ $user->role_label }}
                                            </span>
                                        @endif

                                        @if($user->bidang === 'ekop')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Bidang Ekop
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Associated OPD -->
                                <td class="px-6 py-4">
                                    @if($user->isOpd() && $user->opd)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md bg-[#f0f5f2] text-[#064e3b] font-mono text-[11px] font-bold border border-emerald-200 shrink-0">
                                                {{ $user->opd->kode_opd }}
                                            </span>
                                            <span class="text-xs text-slate-700 font-medium truncate max-w-[220px]" title="{{ $user->opd->nama_opd }}">
                                                {{ $user->opd->nama_opd }}
                                            </span>
                                        </div>
                                    @elseif($user->isOpd() && ! $user->opd)
                                        <span class="text-xs text-rose-500 font-medium italic flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">warning</span>
                                            Belum terhubung ke OPD
                                        </span>
                                    @elseif($user->isAdmin())
                                        @if($user->handledOpds->isNotEmpty())
                                            <div class="space-y-1 max-w-xs">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @foreach($user->handledOpds->take(3) as $hOpd)
                                                        <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 font-mono text-[11px] font-bold border border-teal-200/70" title="{{ $hOpd->nama_opd }}">
                                                            {{ $hOpd->kode_opd }}
                                                        </span>
                                                    @endforeach
                                                    @if($user->handledOpds->count() > 3)
                                                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[10px] border border-slate-200">
                                                            +{{ $user->handledOpds->count() - 3 }} lagi
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[11px] text-slate-400 font-medium block">
                                                    {{ $user->handledOpds->count() }} OPD dikelola
                                                </span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 text-[11px] font-semibold border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Belum ada OPD ditugaskan
                                            </span>
                                        @endif
                                    @elseif($user->isSuperAdmin())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                            Semua OPD (Akses Penuh)
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- Registered At -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                    {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-[#f0f5f2] text-slate-400 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[26px]">person_off</span>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Tidak ada data pengguna yang sesuai pencarian.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci atau filter peran.</p>
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

        {{-- ===== MODAL 1: Konfirmasi Hapus Data ===== --}}
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
                        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus Data</h3>
                        <p class="text-xs text-slate-500">Tindakan ini permanen dan tidak dapat dibatalkan</p>
                    </div>
                </div>

                {{-- Jika ada data yang dipilih --}}
                <template x-if="selectedUsers.length > 0">
                    <div>
                        <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                            Apakah Anda yakin ingin menghapus <strong class="text-rose-700 font-black" x-text="selectedUsers.length + ' data akun'"></strong> yang dipilih? Akun-akun tersebut tidak akan dapat login lagi ke dalam sistem MONITORA.
                        </p>
                        <div class="mb-4 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Ingin hapus semua data?</span>
                            <button type="button" 
                                    @click="selectAll()" 
                                    x-show="!isAllSelected()" 
                                    class="text-[#059669] hover:underline font-bold cursor-pointer">
                                Pilih Seluruh Data ({{ count($selectableUserIds) }} akun)
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Jika belum ada data yang dipilih --}}
                <template x-if="selectedUsers.length === 0">
                    <div class="mb-6">
                        <p class="text-sm text-slate-600 mb-3 leading-relaxed">
                            Belum ada akun yang dicentang. Anda dapat memilih untuk <strong class="text-rose-700 font-bold">menghapus seluruh data akun</strong> di halaman ini ({{ count($selectableUserIds) }} akun, kecuali akun Anda sendiri), atau membatalkan untuk memilih akun tertentu.
                        </p>
                        <button type="button" 
                                @click="selectAll()" 
                                class="w-full py-2.5 px-4 rounded-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 border border-emerald-200 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px] text-[#059669]">check_circle</span>
                            <span>Centang Seluruh Data Akun ({{ count($selectableUserIds) }} Akun)</span>
                        </button>
                    </div>
                </template>

                <form method="POST" action="{{ route('admin.users.bulk-delete') }}" class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-slate-100">
                    @csrf
                    <!-- Hidden inputs of selected user IDs -->
                    <template x-for="userId in selectedUsers" :key="userId">
                        <input type="hidden" name="user_ids[]" :value="userId">
                    </template>

                    <button type="button" 
                            @click="showDeleteModal = false" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="selectedUsers.length === 0"
                            :class="selectedUsers.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-rose-600 hover:bg-rose-700 cursor-pointer'"
                            class="px-6 py-2.5 rounded-full text-white font-bold text-xs shadow-sm transition">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== MODAL CREATE PENGGUNA (Popup Tambah Pengguna Baru) ===== --}}
        <div x-show="showCreateUserModal" 
             x-cloak
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div @click.away="showCreateUserModal = false" 
                 class="bg-white rounded-[28px] max-w-lg w-full shadow-2xl border border-emerald-100 relative flex flex-col max-h-[88vh] overflow-hidden">
                
                <!-- Modal Header (Fixed at top with rounded corners) -->
                <div class="flex items-start justify-between gap-3 p-5 sm:px-7 sm:pt-6 sm:pb-4 border-b border-emerald-100/70 shrink-0 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">person_add</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight leading-snug">Tambah Pengguna Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun Admin, Anggota Bidang, atau Staf OPD</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="showCreateUserModal = false" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Modal Form (Scrollable body inside unclipped rounded card) -->
                <form method="POST" action="{{ route('admin.users.store') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div class="p-5 sm:px-7 overflow-y-auto flex-1 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" required placeholder="Contoh: Ahmad Yani, S.T." 
                                       class="w-full text-sm font-semibold rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Email Login <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" required placeholder="email@malutprov.go.id" 
                                       class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nomor WhatsApp / HP
                                </label>
                                <input type="text" name="no_hp" placeholder="081234567890" 
                                       class="w-full text-sm font-mono rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Bidang Internal Bappeda
                                </label>
                                <select name="bidang" 
                                        class="w-full text-sm rounded-xl border border-transparent bg-[#f0f5f2] focus:bg-white focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 font-medium transition-all">
                                    <option value="">-- Bukan Internal / Umum --</option>
                                    <option value="ekop">Bidang Perekonomian &amp; Perdagangan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Role Selector (Cards Style) -->
                        <div class="pt-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Pilih Peran Akun (Role) <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="newUserRole === 'admin' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="admin" x-model="newUserRole" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">PIC Bappeda</div>
                                        <div class="text-[10px] text-slate-500">Admin pengawas</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="newUserRole === 'opd' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="opd" x-model="newUserRole" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Staf OPD</div>
                                        <div class="text-[10px] text-slate-500">Pengunggah berkas</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="newUserRole === 'super_admin' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="super_admin" x-model="newUserRole" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Super Admin</div>
                                        <div class="text-[10px] text-slate-500">Akses penuh sistem</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic OPD Dropdown for Staf OPD -->
                        <div x-show="newUserRole === 'opd'" x-transition class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Pilih Instansi OPD <span class="text-rose-500">*</span>
                            </label>
                            <select name="opd_id" class="w-full text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2 font-medium">
                                <option value="">-- Pilih Instansi OPD Terdaftar --</option>
                                @foreach($opds ?? [] as $opdItem)
                                    <option value="{{ $opdItem->id }}">[{{ $opdItem->kode_opd }}] {{ $opdItem->nama_opd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Handled OPDs for Admin PIC -->
                        <div x-show="newUserRole === 'admin'" x-transition class="p-3 rounded-2xl bg-teal-50/60 border border-teal-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                OPD yang Dikelola (Opsional)
                            </label>
                            <div class="max-h-28 overflow-y-auto space-y-1 bg-white p-2 rounded-xl border border-slate-200 text-xs">
                                @foreach($opds ?? [] as $opdItem)
                                    <label class="flex items-center gap-2 hover:bg-slate-50 p-1 rounded cursor-pointer">
                                        <input type="checkbox" name="handled_opd_ids[]" value="{{ $opdItem->id }}" class="rounded text-[#059669] focus:ring-[#059669]">
                                        <span class="text-slate-700">[{{ $opdItem->kode_opd }}] {{ $opdItem->nama_opd }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required placeholder="Min. 8 karakter" 
                                       class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Ulangi Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required placeholder="Ulangi password" 
                                       class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions (Fixed at bottom with rounded corners) -->
                    <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-100 shrink-0 bg-[#FAFCFB]">
                        <button type="button" 
                                @click="showCreateUserModal = false" 
                                class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Simpan Pengguna</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== MODAL EDIT PENGGUNA (Popup Edit Data Pengguna) ===== --}}
        <div x-show="showEditUserModal" 
             x-cloak
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div @click.away="showEditUserModal = false" 
                 class="bg-white rounded-[28px] max-w-lg w-full shadow-2xl border border-emerald-100 relative flex flex-col max-h-[88vh] overflow-hidden">
                
                <!-- Modal Header (Fixed at top with rounded corners) -->
                <div class="flex items-start justify-between gap-3 p-5 sm:px-7 sm:pt-6 sm:pb-4 border-b border-emerald-100/70 shrink-0 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">manage_accounts</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight leading-snug">Edit Data Pengguna</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi akun, peran, dan hak akses</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="showEditUserModal = false" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Modal Form (Scrollable body inside unclipped rounded card) -->
                <form method="POST" :action="'/admin/users/' + editUser.id" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')

                    <div class="p-5 sm:px-7 overflow-y-auto flex-1 space-y-4">
                        <!-- Switch User Selector -->
                        <div x-show="userList.length > 1" class="pb-1">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Pilih Akun Lain Untuk Diedit:
                            </label>
                            <select @change="selectUserToEdit($event.target.value)" 
                                    :value="editUser.id"
                                    class="w-full text-xs rounded-xl border border-slate-200 bg-[#f0f5f2] px-3 py-2 font-medium">
                                <template x-for="item in userList" :key="item.id">
                                    <option :value="item.id" :selected="Number(item.id) === Number(editUser.id)" x-text="item.name + ' (' + (item.role_label || item.role) + ') - ' + item.email"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" x-model="editUser.name" required 
                                       class="w-full text-sm font-semibold rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Email Login <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" x-model="editUser.email" required 
                                       class="w-full text-sm rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nomor WhatsApp / HP
                                </label>
                                <input type="text" name="no_hp" x-model="editUser.no_hp" 
                                       class="w-full text-sm font-mono rounded-xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Bidang Internal Bappeda
                                </label>
                                <select name="bidang" x-model="editUser.bidang"
                                        class="w-full text-sm rounded-xl border border-transparent bg-[#f0f5f2] focus:bg-white focus:border-[#059669] px-3.5 py-2.5 focus:ring-2 focus:ring-[#059669]/20 font-medium transition-all">
                                    <option value="">-- Bukan Internal / Umum --</option>
                                    <option value="ekop">Bidang Perekonomian &amp; Perdagangan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Role Selector -->
                        <div class="pt-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Peran Akun (Role) <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="editUser.role === 'admin' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="admin" x-model="editUser.role" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">PIC Bappeda</div>
                                        <div class="text-[10px] text-slate-500">Admin pengawas</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="editUser.role === 'opd' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="opd" x-model="editUser.role" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Staf OPD</div>
                                        <div class="text-[10px] text-slate-500">Pengunggah berkas</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="editUser.role === 'super_admin' ? 'bg-emerald-50/70 border-[#059669] ring-2 ring-[#059669]/20' : 'bg-[#f0f5f2] border-transparent hover:border-slate-300'">
                                    <input type="radio" name="role" value="super_admin" x-model="editUser.role" class="text-[#059669] focus:ring-[#059669]">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Super Admin</div>
                                        <div class="text-[10px] text-slate-500">Akses penuh sistem</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic OPD Dropdown for Staf OPD -->
                        <div x-show="editUser.role === 'opd'" x-transition class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Instansi OPD Terhubung <span class="text-rose-500">*</span>
                            </label>
                            <select name="opd_id" x-model="editUser.opd_id" class="w-full text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2 font-medium">
                                <option value="">-- Pilih Instansi OPD --</option>
                                @foreach($opds ?? [] as $opdItem)
                                    <option value="{{ $opdItem->id }}">[{{ $opdItem->kode_opd }}] {{ $opdItem->nama_opd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Handled OPDs for Admin PIC -->
                        <div x-show="editUser.role === 'admin'" x-transition class="p-3 rounded-2xl bg-teal-50/60 border border-teal-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                OPD yang Dikelola (Opsional)
                            </label>
                            <div class="max-h-28 overflow-y-auto space-y-1 bg-white p-2 rounded-xl border border-slate-200 text-xs">
                                @foreach($opds ?? [] as $opdItem)
                                    <label class="flex items-center gap-2 hover:bg-slate-50 p-1 rounded cursor-pointer">
                                        <input type="checkbox" name="handled_opd_ids[]" value="{{ $opdItem->id }}" 
                                               :checked="(editUser.handled_opd_ids || []).includes({{ $opdItem->id }})"
                                               class="rounded text-[#059669] focus:ring-[#059669]">
                                        <span class="text-slate-700">[{{ $opdItem->kode_opd }}] {{ $opdItem->nama_opd }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Optional Password Fields for Edit -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                Ubah Kata Sandi (Kosongkan jika tidak ingin mengubah)
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <input type="password" name="password" placeholder="Password baru (opsional)" 
                                       class="w-full text-xs rounded-xl bg-white border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                                <input type="password" name="password_confirmation" placeholder="Ulangi password baru" 
                                       class="w-full text-xs rounded-xl bg-white border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions (Fixed at bottom with rounded corners) -->
                    <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-100 shrink-0 bg-[#FAFCFB]">
                        <button type="button" 
                                @click="showEditUserModal = false" 
                                class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Perbarui Pengguna</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</x-app-layout>
