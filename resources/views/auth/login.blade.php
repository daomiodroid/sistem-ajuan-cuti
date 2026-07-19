@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 fade-in">
    <!-- Success Alert (e.g. after logout or registration) -->
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 mb-6 rounded-xl border border-green-500/30 bg-green-500/10 text-green-400">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="glassmorphism p-8 rounded-2xl shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="bg-primary-500/10 border border-primary-500/20 w-12 h-12 rounded-2xl flex items-center justify-center text-primary-400 mx-auto">
                <i class="fa-solid fa-lock text-xl"></i>
            </div>
            <h2 class="font-outfit font-extrabold text-2xl text-white">Login</h2>
            <p class="text-xs text-gray-400">Silakan masukkan akun Anda untuk mengajukan cuti</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-sm">
            @csrf

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
                <input type="password" name="password" id="password" required placeholder="******"
                       class="w-full bg-slate-900 border border-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-xl px-4 py-2.5 text-white transition-all outline-none">
                @error('password')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-indigo-600 hover:from-primary-600 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg active:scale-[0.98] mt-2 flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="text-center pt-2">
            <p class="text-xs text-gray-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-primary-400 hover:underline font-semibold">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
