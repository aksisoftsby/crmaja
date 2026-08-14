<x-guest-layout>
    <div>
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Akses staf</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Selamat datang kembali.</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Masuk untuk melanjutkan pekerjaan Anda di Aksi CRM.</p>
    </div>

    <x-auth-session-status class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Email" class="font-bold text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <div class="flex items-center justify-between gap-4"><x-input-label for="password" value="Kata sandi" class="font-bold text-slate-700" />@if (Route::has('password.request'))<a class="text-xs font-bold text-teal-700 transition hover:text-teal-900" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif</div>
            <x-text-input id="password" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <label for="remember_me" class="flex items-center gap-2.5 text-sm font-medium text-slate-600"><input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-600" name="remember"><span>Ingat saya di perangkat ini</span></label>
        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-teal-700 px-4 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-200">Masuk ke Aksi CRM</button>
    </form>

    <div class="mt-7 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm text-slate-600">Akses pelanggan? <a href="{{ route('portal.login') }}" class="font-bold text-teal-700 transition hover:text-teal-900">Masuk ke Client Portal</a></div>
</x-guest-layout>
