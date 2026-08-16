<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Client Portal Aksi CRM untuk mengakses dokumen, proyek, dan layanan perusahaan Anda secara aman.">
    <title>Client Portal — Aksi CRM</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="relative min-h-screen overflow-hidden px-5 py-8 sm:px-8 lg:flex lg:items-center lg:justify-center lg:p-10">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_88%_12%,rgba(45,212,191,0.22),transparent_26%),radial-gradient(circle_at_8%_86%,rgba(20,184,166,0.14),transparent_28%),linear-gradient(135deg,#f8fafc_0%,#f0fdfa_48%,#f8fafc_100%)]"></div>
        <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 shadow-2xl shadow-slate-900/10 backdrop-blur lg:grid-cols-[0.95fr_1.05fr]">
            <section class="flex min-h-[560px] flex-col justify-center px-6 py-10 sm:px-12 lg:px-14">
                <a href="{{ url('/') }}" class="flex items-center gap-3" aria-label="Aksi CRM, beranda"><x-application-logo class="h-12 w-12" /><span class="text-2xl font-extrabold tracking-tight text-slate-900">Aksi<span class="text-teal-700">CRM</span></span></a>
                <div class="mt-12 max-w-md"><p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Client Portal</p><h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Akses informasi perusahaan Anda.</h1><p class="mt-3 text-sm leading-6 text-slate-500">Lihat dokumen penjualan, invoice, perkembangan project, dan ticket layanan dalam satu portal yang aman.</p></div>
                <div x-data class="max-w-md"><form method="POST" action="{{ route('portal.login.store') }}" class="mt-8 space-y-5">@csrf
                    @if($errors->any())<div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errors->first() }}</div>@endif
                    <div><x-input-label for="email" value="Email" class="font-bold text-slate-700"/><x-text-input id="email" x-ref="email" name="email" type="email" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" :value="old('email')" placeholder="nama@perusahaan.com" required autofocus autocomplete="username"/></div>
                    <div><x-input-label for="password" value="Kata sandi" class="font-bold text-slate-700"/><x-text-input id="password" x-ref="password" name="password" type="password" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Masukkan kata sandi" required autocomplete="current-password"/></div>
                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-600"><input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-600"> Ingat saya di perangkat ini</label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-teal-700 px-4 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-200">Masuk ke Client Portal</button>
                </form>
                <section class="mt-5 rounded-xl border border-teal-100 bg-teal-50/70 p-4" aria-label="Akun demo"><div class="flex items-start justify-between gap-4"><div><p class="text-sm font-extrabold text-teal-900">Akun Demo</p><p class="mt-1 text-xs leading-5 text-teal-800">Pilih akun untuk mengisi form, lalu tekan tombol masuk.</p></div><span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-teal-700 shadow-sm">DEMO</span></div><button type="button" data-demo-account="client-portal" @click="$refs.email.value = 'client@aksisoft.test'; $refs.password.value = 'ClientPass123!'; $refs.email.focus()" class="mt-3 w-full rounded-lg border border-teal-200 bg-white px-3 py-2 text-left text-xs transition hover:border-teal-500 hover:bg-teal-50"><span class="block font-bold text-slate-800">Client Portal Demo</span><span class="block text-slate-500">client@aksisoft.test</span></button></section>
                </div>
                <p class="mt-7 max-w-md text-sm text-slate-600">Akun staf? <a href="{{ route('login') }}" class="font-bold text-teal-700 transition hover:text-teal-900">Masuk ke Aksi CRM</a></p>
            </section>

            <section class="relative hidden min-h-[640px] overflow-hidden bg-slate-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_82%_16%,rgba(45,212,191,0.35),transparent_30%),radial-gradient(circle_at_20%_86%,rgba(20,184,166,0.24),transparent_35%)]"></div>
                <div class="relative"><div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.07] px-3.5 py-2 text-sm font-bold text-teal-100"><span class="h-2 w-2 rounded-full bg-teal-300"></span> Terhubung dengan layanan Anda</div><h2 class="mt-10 max-w-md text-4xl font-extrabold leading-tight tracking-tight">Dokumen dan progres, selalu dalam jangkauan.</h2><p class="mt-5 max-w-md text-base leading-7 text-slate-300">Gunakan Client Portal untuk meninjau setiap detail yang dibagikan tim Aksi CRM kepada perusahaan Anda.</p></div>
                <div class="relative space-y-3"><div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 backdrop-blur"><p class="text-sm font-bold">Invoice & Proposal</p><p class="mt-1 text-sm leading-6 text-slate-300">Tinjau nilai, rincian item, serta riwayat pembayaran.</p></div><div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 backdrop-blur"><p class="text-sm font-bold">Project & Ticket</p><p class="mt-1 text-sm leading-6 text-slate-300">Pantau progres pekerjaan dan percakapan layanan Anda.</p></div></div>
            </section>
        </div>
    </main>
</body>
</html>
