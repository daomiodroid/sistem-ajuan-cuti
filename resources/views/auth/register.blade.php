@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-6 fade-in">
    <div class="glassmorphism p-8 rounded-2xl shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="bg-primary-500/10 border border-primary-500/20 w-12 h-12 rounded-2xl flex items-center justify-center text-primary-400 mx-auto">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h2 class="font-outfit font-extrabold text-2xl text-white">Daftar Akun</h2>
            <p class="text-xs text-gray-400">Buat akun pegawai baru untuk mengajukan cuti</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-sm">
            @csrf

            <!-- NIP -->
            <div class="space-y-1.5">
                <label for="nip" class="text-gray-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-id-badge text-gray-500 text-xs"></i> NIP (Nomor Induk Pegawai)
                </label>
                <input type="text" name="nip" id="nip" required placeholder="Contoh: 19920815" value="{{ old('nip') }}"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
                @error('nip')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama -->
            <div class="space-y-1.5">
                <label for="name" class="text-gray-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-user text-gray-500 text-xs"></i> Nama Lengkap
                </label>
                <input type="text" name="name" id="name" required placeholder="Nama Lengkap Anda" value="{{ old('name') }}"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
                @error('name')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-gray-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-envelope text-gray-500 text-xs"></i> Email
                </label>
                <input type="email" name="email" id="email" required placeholder="nama@email.com" value="{{ old('email') }}"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
                @error('email')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-gray-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-gray-500 text-xs"></i> Password
                </label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
                @error('password')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-gray-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-gray-500 text-xs"></i> Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ketik ulang password"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-indigo-600 hover:from-primary-600 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg active:scale-[0.98] mt-2 flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Daftar
            </button>
        </form>

        <div class="text-center pt-2">
            <p class="text-xs text-gray-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-primary-400 hover:underline font-semibold">Login di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
