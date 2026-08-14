<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aksi CRM membantu tim mengelola pelanggan, peluang, penjualan, dan layanan dalam satu ruang kerja.">
    <title>Aksi CRM — Akses Staf</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="relative min-h-screen overflow-hidden px-5 py-8 sm:px-8 lg:flex lg:items-center lg:justify-center lg:p-10">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_12%_16%,rgba(45,212,191,0.22),transparent_27%),radial-gradient(circle_at_90%_82%,rgba(20,184,166,0.16),transparent_30%),linear-gradient(135deg,#f8fafc_0%,#f0fdfa_48%,#f8fafc_100%)]"></div>
        <div class="absolute -left-32 top-1/2 -z-10 h-80 w-80 -translate-y-1/2 rounded-full bg-teal-200/30 blur-3xl"></div>

        <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 shadow-2xl shadow-slate-900/10 backdrop-blur lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden min-h-[640px] overflow-hidden bg-slate-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_12%,rgba(45,212,191,0.35),transparent_32%),radial-gradient(circle_at_86%_82%,rgba(20,184,166,0.25),transparent_32%)]"></div>
                <div class="relative">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3" aria-label="Aksi CRM, beranda">
                        <x-application-logo class="h-12 w-12" />
                        <span class="text-2xl font-extrabold tracking-tight">Aksi<span class="text-teal-300">CRM</span></span>
                    </a>
                    <p class="mt-16 max-w-md text-sm font-bold uppercase tracking-[0.18em] text-teal-200">Ruang kerja tim</p>
                    <h1 class="mt-4 max-w-lg text-4xl font-extrabold leading-tight tracking-tight">Kelola setiap hubungan pelanggan dengan lebih terarah.</h1>
                    <p class="mt-5 max-w-md text-base leading-7 text-slate-300">Akses satu ruang kerja untuk pelanggan, peluang, dokumen penjualan, proyek, dan dukungan layanan.</p>
                </div>
                <div class="relative rounded-2xl border border-white/10 bg-white/[0.07] p-5 backdrop-blur"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-400/15 text-teal-200"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Zm3.53 6.78a.75.75 0 0 1 .01 1.06l-4.25 4.32a.75.75 0 0 1-1.07.01l-1.9-1.84a.75.75 0 1 1 1.04-1.08l1.37 1.33 3.73-3.79a.75.75 0 0 1 1.07-.01Z" clip-rule="evenodd" /></svg></span><div><p class="text-sm font-bold">Akses yang terkontrol</p><p class="mt-0.5 text-xs leading-5 text-slate-300">Gunakan akun staf sesuai role dan wewenang Anda.</p></div></div></div>
            </section>

            <section class="flex min-h-[560px] flex-col justify-center px-6 py-10 sm:px-12 lg:px-14">
                <a href="{{ url('/') }}" class="mx-auto flex items-center gap-2 lg:hidden" aria-label="Aksi CRM, beranda"><x-application-logo class="h-10 w-10" /><span class="text-xl font-extrabold tracking-tight text-slate-900">Aksi<span class="text-teal-700">CRM</span></span></a>
                <div class="mx-auto mt-8 w-full max-w-md lg:mt-0">
                    {{ $slot }}
                </div>
                <p class="mx-auto mt-10 max-w-md text-center text-xs leading-5 text-slate-400">© {{ now()->year }} Aksisoft. Sistem CRM untuk kerja tim yang lebih rapi.</p>
            </section>
        </div>
    </main>
</body>
</html>
