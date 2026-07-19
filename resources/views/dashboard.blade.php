@extends('layouts.app')

@section('content')
<div class="space-y-8 fade-in text-sm">
    <!-- Notifikasi Sukses / Error -->
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl border border-green-500/30 bg-green-500/10 text-green-400">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl border border-red-500/30 bg-red-500/10 text-red-400">
            <i class="fa-solid fa-circle-exclamation text-lg"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl border border-red-500/30 bg-red-500/10 text-red-400">
            <div class="flex items-center gap-3 mb-2 font-semibold">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                <span>Ada kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Banner Info Akun -->
    <div class="glassmorphism p-6 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="font-bold text-xl text-white">Dashboard Pengajuan Cuti</h2>
            <p class="text-gray-400 text-xs mt-1">
                @if(Auth::user()->role === 'admin')
                    Masuk sebagai **Administrator** (Bisa menyetujui atau menolak cuti pegawai).
                @else
                    Masuk sebagai **Pegawai** (Bisa mengajukan cuti dan melihat riwayat ajuan sendiri).
                @endif
            </p>
        </div>
        
        <div class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-slate-400">Akun: <strong>{{ Auth::user()->email }}</strong></span>
        </div>
    </div>

    <!-- Kotak Ringkasan Statistik -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="glass-card p-5 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">Total Pengajuan</span>
                <p class="font-bold text-xl text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-blue-500/10 border border-blue-500/20 w-10 h-10 rounded-lg flex items-center justify-center text-blue-400 text-sm">
                <i class="fa-solid fa-folder"></i>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="glass-card p-5 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">Menunggu</span>
                <p class="font-bold text-xl text-yellow-500 mt-1">{{ $stats['menunggu'] }}</p>
            </div>
            <div class="bg-yellow-500/10 border border-yellow-500/20 w-10 h-10 rounded-lg flex items-center justify-center text-yellow-500 text-sm">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="glass-card p-5 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">Disetujui</span>
                <p class="font-bold text-xl text-green-500 mt-1">{{ $stats['disetujui'] }}</p>
            </div>
            <div class="bg-green-500/10 border border-green-500/20 w-10 h-10 rounded-lg flex items-center justify-center text-green-500 text-sm">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="glass-card p-5 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">Ditolak</span>
                <p class="font-bold text-xl text-red-500 mt-1">{{ $stats['ditolak'] }}</p>
            </div>
            <div class="bg-red-500/10 border border-red-500/20 w-10 h-10 rounded-lg flex items-center justify-center text-red-500 text-sm">
                <i class="fa-solid fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Area Kerja Utama -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
        
        <!-- Sisi Kiri: Form / Panel Info -->
        @if(Auth::user()->role === 'pegawai')
            <!-- Formulir Pengajuan Cuti (Hanya untuk Pegawai) -->
            <div class="glassmorphism p-6 rounded-2xl space-y-5 xl:col-span-1">
                <div class="border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-alt text-primary-400"></i>
                    <h3 class="font-bold text-base text-white">Buat Pengajuan Cuti</h3>
                </div>

                <form action="{{ route('ajuan-cuti.store') }}" method="POST" onsubmit="return validateForm(this)" class="space-y-4">
                    @csrf
                    <!-- NIP (Read-only dari database) -->
                    <div class="space-y-1">
                        <label class="text-gray-400 text-xs font-medium">NIP Pegawai</label>
                        <input type="text" value="{{ Auth::user()->nip }}" disabled
                               class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2 text-slate-500 cursor-not-allowed outline-none">
                    </div>

                    <!-- Nama (Read-only dari database) -->
                    <div class="space-y-1">
                        <label class="text-gray-400 text-xs font-medium">Nama Lengkap</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled
                               class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2 text-slate-500 cursor-not-allowed outline-none">
                    </div>

                    <!-- Jenis Cuti -->
                    <div class="space-y-1">
                        <label for="jenis_cuti" class="text-gray-300 text-xs font-medium">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti" required
                                class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih jenis --</option>
                            <option value="Cuti Tahunan" {{ old('jenis_cuti') === 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                            <option value="Cuti Sakit" {{ old('jenis_cuti') === 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                            <option value="Cuti Alasan Penting" {{ old('jenis_cuti') === 'Cuti Alasan Penting' ? 'selected' : '' }}>Cuti Alasan Penting</option>
                            <option value="Cuti Melahirkan" {{ old('jenis_cuti') === 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                        </select>
                    </div>

                    <!-- Tanggal Cuti -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="tanggal_mulai" class="text-gray-300 text-xs font-medium">Tgl Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" required value="{{ old('tanggal_mulai') }}"
                                   class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none text-xs">
                        </div>
                        <div class="space-y-1">
                            <label for="tanggal_selesai" class="text-gray-300 text-xs font-medium">Tgl Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" required value="{{ old('tanggal_selesai') }}"
                                   class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none text-xs">
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-1">
                        <label for="keterangan" class="text-gray-300 text-xs font-medium">Alasan Cuti</label>
                        <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tulis keterangan atau alasan cuti..."
                                  class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none resize-none">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Kirim Button -->
                    <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-2.5 rounded-xl transition-all shadow-lg active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
                    </button>
                </form>
            </div>
        @else
            <!-- Panel Info Admin (Hanya untuk Admin) -->
            <div class="glassmorphism p-6 rounded-2xl space-y-4 xl:col-span-1 border-purple-500/20 bg-purple-950/5">
                <div class="border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-purple-400"></i>
                    <h3 class="font-bold text-base text-purple-300">Panel Administrator</h3>
                </div>
                <p class="text-slate-300 text-xs leading-relaxed">
                    Anda login menggunakan akun Admin. Anda dapat meninjau semua pengajuan cuti pegawai yang masuk.
                </p>
                <div class="p-3.5 rounded-xl bg-purple-950/20 border border-purple-900/30 text-xs text-slate-400 leading-normal space-y-2">
                    <p class="font-semibold text-purple-300">Langkah Persetujuan Cuti:</p>
                    <ul class="list-decimal list-inside space-y-1 pl-1 text-[11px]">
                        <li>Lihat data pengajuan pada tabel riwayat.</li>
                        <li>Tekan tombol <span class="text-green-400 font-bold">Setujui</span> untuk menerima permohonan.</li>
                        <li>Tekan tombol <span class="text-red-400 font-bold">Tolak</span> jika permohonan tidak diizinkan.</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Sisi Kanan: Tabel Riwayat Cuti -->
        <div class="glassmorphism p-6 rounded-2xl space-y-5 xl:col-span-2">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-history text-slate-400"></i>
                    <h3 class="font-bold text-base text-white">Daftar Cuti</h3>
                </div>

                <!-- Form Filter & Pencarian -->
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <!-- Search input (hanya untuk admin agar bisa cari nama/nip) -->
                    @if(Auth::user()->role === 'admin')
                        <div class="relative flex-grow sm:flex-grow-0">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIP..."
                                   class="bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl pl-8 pr-3 py-1.5 text-xs text-white outline-none w-full sm:w-40">
                            <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-gray-600 text-xs"></i>
                        </div>
                    @endif
                    
                    <!-- Filter Status -->
                    <select name="status" onchange="this.form.submit()"
                            class="bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-2 py-1.5 text-xs text-white outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" {{ request('status') === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    @if(request('search') || request('status'))
                        <a href="{{ route('dashboard') }}" class="text-xs text-slate-400 hover:text-white font-medium hover:bg-slate-800 px-2 py-1.5 rounded-lg">
                            Batal Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto rounded-xl border border-slate-800/60 bg-slate-950/20">
                <table class="w-full text-left text-xs md:text-sm">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold">Pegawai</th>
                            <th class="py-3.5 px-4 font-semibold">Jenis Cuti / Alasan</th>
                            <th class="py-3.5 px-4 font-semibold">Tanggal</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @forelse($ajuanCutis as $ajuan)
                            <tr class="hover:bg-slate-800/10 text-slate-300">
                                <!-- Pegawai -->
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-white text-xs sm:text-sm">{{ $ajuan->nama }}</p>
                                    <p class="text-[10px] text-gray-500 font-semibold tracking-wider uppercase mt-0.5">
                                        NIP: {{ $ajuan->nip }}
                                    </p>
                                </td>
                                <!-- Cuti & Alasan -->
                                <td class="py-3.5 px-4 max-w-[200px]">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-wide
                                        {{ $ajuan->jenis_cuti === 'Cuti Tahunan' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                        {{ $ajuan->jenis_cuti === 'Cuti Sakit' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ $ajuan->jenis_cuti === 'Cuti Alasan Penting' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                        {{ $ajuan->jenis_cuti === 'Cuti Melahirkan' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                    ">
                                        {{ $ajuan->jenis_cuti }}
                                    </span>
                                    <p class="text-xs text-gray-400 truncate mt-1" title="{{ $ajuan->keterangan }}">
                                        {{ $ajuan->keterangan ?: '-' }}
                                    </p>
                                </td>
                                <!-- Tanggal -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <p class="text-slate-300 font-medium">{{ $ajuan->tanggal_mulai->format('d M Y') }}</p>
                                    <p class="text-slate-500 text-[10px] mt-0.5">s.d. {{ $ajuan->tanggal_selesai->format('d M Y') }}</p>
                                </td>
                                <!-- Status -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase
                                        {{ $ajuan->status === 'Menunggu' ? 'bg-yellow-500/15 text-yellow-500 border border-yellow-500/20 animate-pulse' : '' }}
                                        {{ $ajuan->status === 'Disetujui' ? 'bg-green-500/15 text-green-500 border border-green-500/20' : '' }}
                                        {{ $ajuan->status === 'Ditolak' ? 'bg-red-500/15 text-red-500 border border-red-500/20' : '' }}
                                    ">
                                        {{ $ajuan->status }}
                                    </span>
                                </td>
                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if(Auth::user()->role === 'pegawai')
                                        <!-- Aksi Pegawai -->
                                        @if($ajuan->status === 'Menunggu')
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="openEditModal({{ json_encode($ajuan) }})" 
                                                        class="h-7 w-7 rounded bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center"
                                                        title="Ubah data">
                                                    <i class="fa-solid fa-edit text-xs"></i>
                                                </button>
                                                
                                                <form action="{{ route('ajuan-cuti.destroy', $ajuan->id) }}" method="POST" onsubmit="return confirm('Batalkan ajuan cuti ini?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="h-7 w-7 rounded bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"
                                                            title="Batalkan">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500 italic">Sudah Diproses</span>
                                        @endif
                                    @else
                                        <!-- Aksi Admin -->
                                        @if($ajuan->status === 'Menunggu')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <form action="{{ route('ajuan-cuti.status', $ajuan->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Disetujui">
                                                    <button type="submit" class="px-2 py-1 rounded bg-green-500/15 border border-green-500/30 text-green-400 hover:bg-green-500 hover:text-white font-semibold transition-all text-[11px]">
                                                        Setujui
                                                    </button>
                                                </form>

                                                <form action="{{ route('ajuan-cuti.status', $ajuan->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Ditolak">
                                                    <button type="submit" class="px-2 py-1 rounded bg-red-500/15 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white font-semibold transition-all text-[11px]">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-500 flex items-center gap-1 justify-center">
                                                <i class="fa-solid fa-lock text-[10px]"></i> Selesai
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-folder-open text-2xl text-gray-600"></i>
                                        <p class="font-medium text-gray-400">Belum ada pengajuan cuti</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Navigasi Halaman -->
            <div class="pt-2">
                {{ $ajuanCutis->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL POPUP EDIT (KHUSUS PEGAWAI) -->
<!-- ============================================== -->
@if(Auth::user()->role === 'pegawai')
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
    <div class="glassmorphism w-full max-w-md rounded-2xl overflow-hidden shadow-2xl scale-95 transition-transform duration-300" id="modal-content">
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-900/40">
            <h3 class="font-bold text-base text-white flex items-center gap-2">
                <i class="fa-solid fa-edit text-primary-400"></i> Ubah Pengajuan Cuti
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-white">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Form Modal -->
        <form id="form-edit" method="POST" onsubmit="return validateForm(this)" class="p-6 space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')
            
            <!-- NIP (Disabled) -->
            <div class="space-y-1">
                <label class="text-gray-400 text-xs font-medium">NIP Pegawai</label>
                <input type="text" value="{{ Auth::user()->nip }}" disabled
                       class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2 text-slate-500 cursor-not-allowed outline-none">
            </div>

            <!-- Nama (Disabled) -->
            <div class="space-y-1">
                <label class="text-gray-400 text-xs font-medium">Nama Lengkap</label>
                <input type="text" value="{{ Auth::user()->name }}" disabled
                       class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2 text-slate-500 cursor-not-allowed outline-none">
            </div>

            <!-- Jenis Cuti -->
            <div class="space-y-1">
                <label for="edit_jenis_cuti" class="text-gray-300 text-xs font-medium">Jenis Cuti</label>
                <select name="jenis_cuti" id="edit_jenis_cuti" required
                        class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none cursor-pointer text-xs sm:text-sm">
                    <option value="Cuti Tahunan">Cuti Tahunan</option>
                    <option value="Cuti Sakit">Cuti Sakit</option>
                    <option value="Cuti Alasan Penting">Cuti Alasan Penting</option>
                    <option value="Cuti Melahirkan">Cuti Melahirkan</option>
                </select>
            </div>

            <!-- Tanggal Cuti -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="edit_tanggal_mulai" class="text-gray-300 text-xs font-medium">Tgl Mulai</label>
                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required
                           class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none text-xs">
                </div>
                <div class="space-y-1">
                    <label for="edit_tanggal_selesai" class="text-gray-300 text-xs font-medium">Tgl Selesai</label>
                    <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required
                           class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none text-xs">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1">
                <label for="edit_keterangan" class="text-gray-300 text-xs font-medium">Alasan Cuti</label>
                <textarea name="keterangan" id="edit_keterangan" rows="3" placeholder="Tulis keterangan..."
                          class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 rounded-xl px-4 py-2 text-white outline-none resize-none"></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white transition-all font-semibold text-xs sm:text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-bold transition-all text-xs sm:text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ============================================== -->
<!-- SCRIPT JS VALIDASI & MODAL -->
<!-- ============================================== -->
<script>
    // Validasi Tanggal Selesai >= Tanggal Mulai
    function validateForm(form) {
        const tglMulaiStr = form.querySelector('[name="tanggal_mulai"]').value;
        const tglSelesaiStr = form.querySelector('[name="tanggal_selesai"]').value;
        
        if (tglMulaiStr && tglSelesaiStr) {
            const start = new Date(tglMulaiStr);
            const end = new Date(tglSelesaiStr);
            
            if (end < start) {
                alert('Peringatan: Tanggal selesai tidak boleh mendahului tanggal mulai!');
                return false;
            }
        }
        return true;
    }

    @if(Auth::user()->role === 'pegawai')
    // Buka Modal Edit
    function openEditModal(ajuan) {
        const modal = document.getElementById('modal-edit');
        const modalContent = document.getElementById('modal-content');
        const form = document.getElementById('form-edit');
        
        document.getElementById('edit_jenis_cuti').value = ajuan.jenis_cuti;
        
        const parseDate = (dStr) => {
            const d = new Date(dStr);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        };
        
        document.getElementById('edit_tanggal_mulai').value = parseDate(ajuan.tanggal_mulai);
        document.getElementById('edit_tanggal_selesai').value = parseDate(ajuan.tanggal_selesai);
        document.getElementById('edit_keterangan').value = ajuan.keterangan || '';
        
        form.action = `/ajuan-cuti/${ajuan.id}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    // Tutup Modal Edit
    function closeEditModal() {
        const modal = document.getElementById('modal-edit');
        const modalContent = document.getElementById('modal-content');
        
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 150);
    }

    // Tutup modal jika klik di luar area card
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modal-edit');
        if (e.target === modal) {
            closeEditModal();
        }
    });
    @endif
</script>
@endsection
