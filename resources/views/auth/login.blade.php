<x-guest-layout>
    <div>
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Akses staf</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Selamat datang kembali.</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Masuk untuk melanjutkan pekerjaan Anda di Aksi CRM.</p>
    </div>

    <x-auth-session-status class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" :status="session('status')" />

    <div x-data>
    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Email" class="font-bold text-slate-700" />
            <x-text-input id="email" x-ref="email" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <div class="flex items-center justify-between gap-4"><x-input-label for="password" value="Kata sandi" class="font-bold text-slate-700" />@if (Route::has('password.request'))<a class="text-xs font-bold text-teal-700 transition hover:text-teal-900" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif</div>
            <x-text-input id="password" x-ref="password" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <label for="remember_me" class="flex items-center gap-2.5 text-sm font-medium text-slate-600"><input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-600" name="remember"><span>Ingat saya di perangkat ini</span></label>
        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-teal-700 px-4 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-200">Masuk ke Aksi CRM</button>
    </form>

    <section class="mt-5 rounded-xl border border-teal-100 bg-teal-50/70 p-4" aria-label="Akun demo">
        <div class="flex items-start justify-between gap-4"><div><p class="text-sm font-extrabold text-teal-900">Akun Demo</p><p class="mt-1 text-xs leading-5 text-teal-800">Pilih akun untuk mengisi form, lalu tekan tombol masuk.</p></div><span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-teal-700 shadow-sm">DEMO</span></div>
        <div class="mt-3 grid gap-2 sm:grid-cols-3">
            <button type="button" data-demo-account="super-admin" @click="$refs.email.value = 'admin@aksisoft.test'; $refs.password.value = 'ChangeMe123!'; $refs.email.focus()" class="rounded-lg border border-teal-200 bg-white px-3 py-2 text-left text-xs transition hover:border-teal-500 hover:bg-teal-50"><span class="block font-bold text-slate-800">Super Admin</span><span class="block truncate text-slate-500">admin@aksisoft.test</span></button>
            <button type="button" data-demo-account="sales" @click="$refs.email.value = 'sales@demo.aksisoft.test'; $refs.password.value = 'DemoRolePass123!'; $refs.email.focus()" class="rounded-lg border border-teal-200 bg-white px-3 py-2 text-left text-xs transition hover:border-teal-500 hover:bg-teal-50"><span class="block font-bold text-slate-800">Sales Demo</span><span class="block truncate text-slate-500">sales@demo.aksisoft.test</span></button>
            <button type="button" data-demo-account="support" @click="$refs.email.value = 'support@demo.aksisoft.test'; $refs.password.value = 'DemoRolePass123!'; $refs.email.focus()" class="rounded-lg border border-teal-200 bg-white px-3 py-2 text-left text-xs transition hover:border-teal-500 hover:bg-teal-50"><span class="block font-bold text-slate-800">Support Demo</span><span class="block truncate text-slate-500">support@demo.aksisoft.test</span></button>
        </div>
    </section>
    </div>

    <div class="mt-7 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm text-slate-600">Akses pelanggan? <a href="{{ route('portal.login') }}" class="font-bold text-teal-700 transition hover:text-teal-900">Masuk ke Client Portal</a></div>
</x-guest-layout>
