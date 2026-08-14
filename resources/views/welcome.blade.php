<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aksi CRM menyatukan sales, pelanggan, dokumen penjualan, proyek, dan dukungan dalam satu ruang kerja yang rapi.">
    <title>Aksi CRM — Operasi Pelanggan Lebih Terkendali</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-teal-600 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">Langsung ke konten</a>

    <div class="relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 -z-10 h-[680px] bg-[radial-gradient(circle_at_78%_18%,rgba(45,212,191,0.23),transparent_30%),radial-gradient(circle_at_17%_8%,rgba(20,184,166,0.15),transparent_27%),linear-gradient(180deg,#f0fdfa_0%,#f8fafc_70%,#f8fafc_100%)]"></div>
        <div class="absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-teal-200/40 blur-3xl"></div>

        <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ url('/') }}" class="flex items-center gap-3" aria-label="Aksi CRM, beranda">
                <x-application-logo class="h-10 w-10 shrink-0" />
                <span class="text-xl font-extrabold tracking-tight text-slate-950">Aksi<span class="text-teal-700">CRM</span></span>
            </a>
            <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 md:flex">
                <a href="#fitur" class="transition hover:text-teal-700">Fitur</a>
                <a href="#cara-kerja" class="transition hover:text-teal-700">Cara kerja</a>
                <a href="#paket" class="transition hover:text-teal-700">Solusi</a>
            </nav>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="hidden text-sm font-semibold text-slate-700 transition hover:text-teal-700 sm:inline">Buka dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-slate-700 transition hover:text-teal-700 sm:inline">Masuk</a>
                @endauth
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-200">Coba Sekarang</a>
            </div>
        </header>

        <main id="konten">
            <section class="mx-auto grid max-w-7xl gap-12 px-6 pb-20 pt-16 lg:grid-cols-[1.02fr_0.98fr] lg:items-center lg:px-8 lg:pb-28 lg:pt-24">
                <div class="max-w-2xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-teal-200 bg-white/80 px-3.5 py-2 text-sm font-semibold text-teal-800 shadow-sm backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                        CRM terpadu untuk bisnis Indonesia
                    </div>
                    <h1 class="text-4xl font-extrabold leading-[1.08] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">Kendalikan pertumbuhan pelanggan, <span class="text-teal-700">tanpa kerja yang tercecer.</span></h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">Aksi CRM menyatukan prospek, customer, proposal, invoice, proyek, dan dukungan pelanggan dalam satu sistem yang mudah dipakai tim Anda.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-700 px-5 py-3.5 text-base font-bold text-white shadow-xl shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-200">
                            Mulai gunakan Aksi CRM
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.22 3.22a.75.75 0 0 1 1.06 0l6.25 6.25a.75.75 0 0 1 0 1.06l-6.25 6.25a.75.75 0 0 1-1.06-1.06l4.97-4.97H3.25a.75.75 0 0 1 0-1.5h11.94l-4.97-4.97a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#fitur" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3.5 text-base font-bold text-slate-700 shadow-sm transition hover:border-teal-300 hover:text-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-100">Jelajahi fitur</a>
                    </div>
                    <div class="mt-9 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-slate-600">
                        <span class="inline-flex items-center gap-2"><svg class="h-5 w-5 text-teal-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.6a1 1 0 0 1-1.42.006l-3.5-3.4a1 1 0 1 1 1.394-1.436l2.789 2.71 6.803-6.89a1 1 0 0 1 1.428-.004Z" clip-rule="evenodd" /></svg>Data terpusat</span>
                        <span class="inline-flex items-center gap-2"><svg class="h-5 w-5 text-teal-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.6a1 1 0 0 1-1.42.006l-3.5-3.4a1 1 0 1 1 1.394-1.436l2.789 2.71 6.803-6.89a1 1 0 0 1 1.428-.004Z" clip-rule="evenodd" /></svg>Alur kerja terpadu</span>
                        <span class="inline-flex items-center gap-2"><svg class="h-5 w-5 text-teal-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.6a1 1 0 0 1-1.42.006l-3.5-3.4a1 1 0 1 1 1.394-1.436l2.789 2.71 6.803-6.89a1 1 0 0 1 1.428-.004Z" clip-rule="evenodd" /></svg>Akses berbasis peran</span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] bg-teal-300/25 blur-3xl"></div>
                    <div class="overflow-hidden rounded-2xl border border-white/80 bg-white/90 p-4 shadow-2xl shadow-slate-900/15 backdrop-blur">
                        <div class="flex items-center justify-between border-b border-slate-100 px-2 pb-3">
                            <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
                            <span class="text-xs font-bold tracking-wide text-slate-400">DASHBOARD AKSI CRM</span>
                        </div>
                        <div class="grid gap-4 p-2 pt-5 sm:grid-cols-[0.62fr_1.38fr]">
                            <aside class="rounded-xl bg-slate-950 p-4 text-slate-300">
                                <div class="flex items-center gap-2 border-b border-white/10 pb-4"><x-application-logo class="h-7 w-7" /><span class="text-sm font-bold text-white">Aksi CRM</span></div>
                                <div class="mt-5 space-y-3 text-xs font-medium">
                                    <div class="rounded-lg bg-teal-500/15 px-3 py-2 text-teal-300">Ringkasan</div>
                                    <div class="px-3">Pelanggan</div><div class="px-3">Lead</div><div class="px-3">Penjualan</div><div class="px-3">Proyek</div><div class="px-3">Tiket</div>
                                </div>
                            </aside>
                            <div class="space-y-4">
                                <div class="flex items-start justify-between"><div><p class="text-xs font-semibold text-slate-400">Selamat datang kembali</p><h2 class="mt-1 text-lg font-extrabold text-slate-900">Ringkasan hari ini</h2></div><div class="rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-700">Live</div></div>
                                <div class="grid grid-cols-3 gap-2.5">
                                    <div class="rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Lead aktif</p><p class="mt-1 text-xl font-extrabold text-slate-900">24</p><p class="mt-1 text-[10px] font-semibold text-emerald-600">+12% minggu ini</p></div>
                                    <div class="rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Proposal</p><p class="mt-1 text-xl font-extrabold text-slate-900">18</p><p class="mt-1 text-[10px] font-semibold text-teal-600">Butuh tindak lanjut</p></div>
                                    <div class="rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiket</p><p class="mt-1 text-xl font-extrabold text-slate-900">07</p><p class="mt-1 text-[10px] font-semibold text-amber-600">Menunggu respons</p></div>
                                </div>
                                <div class="rounded-xl border border-slate-100 p-4"><div class="flex items-center justify-between"><p class="text-xs font-bold text-slate-700">Pipeline penjualan</p><p class="text-[10px] font-semibold text-slate-400">Bulan ini</p></div><div class="mt-4 flex h-24 items-end gap-2"><div class="h-[38%] flex-1 rounded-t-md bg-teal-100"></div><div class="h-[63%] flex-1 rounded-t-md bg-teal-200"></div><div class="h-[47%] flex-1 rounded-t-md bg-teal-300"></div><div class="h-[82%] flex-1 rounded-t-md bg-teal-500"></div><div class="h-[68%] flex-1 rounded-t-md bg-teal-400"></div><div class="h-[94%] flex-1 rounded-t-md bg-teal-700"></div></div></div>
                                <div class="rounded-xl border border-slate-100 p-4"><div class="flex items-center justify-between"><p class="text-xs font-bold text-slate-700">Aktivitas terbaru</p><a href="#fitur" class="text-[10px] font-bold text-teal-700">Lihat semua</a></div><div class="mt-3 space-y-2"><div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-teal-500"></span><span class="h-2 flex-1 rounded bg-slate-100"></span></div><div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-400"></span><span class="h-2 w-3/4 rounded bg-slate-100"></span></div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-white/80" aria-label="Nilai utama">
                <div class="mx-auto grid max-w-7xl gap-6 px-6 py-7 text-center sm:grid-cols-3 lg:px-8">
                    <div><p class="text-xl font-extrabold text-slate-950">Satu sumber data</p><p class="mt-1 text-sm text-slate-500">Untuk seluruh perjalanan pelanggan</p></div>
                    <div class="border-slate-200 sm:border-x sm:px-6"><p class="text-xl font-extrabold text-slate-950">Lebih sedikit perpindahan</p><p class="mt-1 text-sm text-slate-500">Antar spreadsheet dan aplikasi</p></div>
                    <div><p class="text-xl font-extrabold text-slate-950">Lebih banyak kendali</p><p class="mt-1 text-sm text-slate-500">Atas proses dan tindak lanjut</p></div>
                </div>
            </section>

            <section id="fitur" class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
                <div class="max-w-2xl"><p class="text-sm font-extrabold uppercase tracking-[0.16em] text-teal-700">Semua yang tim butuhkan</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Dari peluang pertama hingga pelanggan setia.</h2><p class="mt-4 text-lg leading-8 text-slate-600">Setiap modul dirancang untuk saling terhubung, sehingga informasi selalu tersedia ketika tim membutuhkan keputusan yang cepat dan tepat.</p></div>
                <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['01', 'Pelanggan & kontak', 'Simpan profil perusahaan, kontak utama, catatan, dan riwayat transaksi dalam satu tampilan.'],
                        ['02', 'Lead & pipeline', 'Kelola sumber prospek, status peluang, nilai penjualan, dan tindak lanjut tim sales.'],
                        ['03', 'Proposal & estimasi', 'Buat dokumen penawaran yang rapi, lacak statusnya, dan percepat persetujuan pelanggan.'],
                        ['04', 'Invoice & pembayaran', 'Pantau tagihan, pembayaran parsial, jatuh tempo, serta dokumen PDF tanpa data terpisah.'],
                        ['05', 'Proyek & tugas', 'Hubungkan komitmen penjualan dengan eksekusi melalui milestone, tugas, waktu, dan kolaborasi.'],
                        ['06', 'Tiket & knowledge base', 'Berikan layanan yang konsisten lewat tiket, respons tim, dan artikel panduan mandiri.'],
                    ] as [$number, $title, $description])
                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-900/5">
                            <div class="flex items-start justify-between"><span class="text-sm font-extrabold text-teal-700">{{ $number }}</span><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition group-hover:bg-teal-700 group-hover:text-white"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 1 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" class="hidden"/><path d="M10.75 4.5a.75.75 0 0 0-1.5 0v4.75H4.5a.75.75 0 0 0 0 1.5h4.75v4.75a.75.75 0 0 0 1.5 0v-4.75h4.75a.75.75 0 0 0 0-1.5h-4.75V4.5Z"/></svg></span></div>
                            <h3 class="mt-5 text-lg font-extrabold text-slate-950">{{ $title }}</h3><p class="mt-2 leading-7 text-slate-600">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="cara-kerja" class="bg-slate-950 py-20 text-white lg:py-28">
                <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                    <div><p class="text-sm font-extrabold uppercase tracking-[0.16em] text-teal-300">Alur yang menyatu</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Kerja tim menjadi jelas dari awal hingga akhir.</h2><p class="mt-5 max-w-md text-lg leading-8 text-slate-300">Aksi CRM memberi konteks pada setiap langkah. Sales tahu peluang yang harus dikejar, proyek tahu janji yang perlu dipenuhi, dan support tahu riwayat pelanggan.</p><a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-teal-100 focus:outline-none focus:ring-4 focus:ring-teal-300">Masuk ke aplikasi</a></div>
                    <ol class="space-y-3">
                        @foreach ([['01', 'Tangkap peluang', 'Masukkan lead dari website, referral, WhatsApp, email, atau pameran.'], ['02', 'Menangkan penjualan', 'Kirim proposal, estimasi, invoice, dan pantau pembayaran secara terhubung.'], ['03', 'Jalankan pekerjaan', 'Konversi komitmen menjadi proyek dan tugas yang dapat dipantau.'], ['04', 'Layani dengan konsisten', 'Kelola tiket, percakapan, dan knowledge base untuk pengalaman pelanggan yang lebih baik.']] as [$number, $title, $description])
                            <li class="flex gap-5 rounded-2xl border border-white/10 bg-white/[0.04] p-5 transition hover:border-teal-300/40 hover:bg-white/[0.07]"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-400/15 text-sm font-extrabold text-teal-200">{{ $number }}</span><div><h3 class="font-extrabold">{{ $title }}</h3><p class="mt-1 text-sm leading-6 text-slate-300">{{ $description }}</p></div></li>
                        @endforeach
                    </ol>
                </div>
            </section>

            <section id="paket" class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
                <div class="grid gap-12 rounded-3xl bg-teal-700 px-7 py-10 text-white shadow-xl shadow-teal-900/15 lg:grid-cols-[1.2fr_0.8fr] lg:px-12 lg:py-14">
                    <div><p class="text-sm font-extrabold uppercase tracking-[0.16em] text-teal-100">Siap merapikan operasi pelanggan?</p><h2 class="mt-3 max-w-xl text-3xl font-extrabold tracking-tight sm:text-4xl">Mulai dari alur kerja yang sudah Anda miliki, lalu tumbuh dengan lebih terukur.</h2><p class="mt-5 max-w-xl text-lg leading-8 text-teal-50">Aksi CRM menghadirkan fondasi operasional yang lengkap untuk tim sales, layanan, dan delivery dalam satu pengalaman yang konsisten.</p></div>
                    <div class="flex flex-col justify-center rounded-2xl bg-white p-6 text-slate-900 shadow-lg"><p class="text-sm font-extrabold text-teal-700">Akses aplikasi</p><p class="mt-2 text-2xl font-extrabold">Mulai kelola CRM Anda hari ini.</p><p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun staf untuk mengakses dashboard atau portal pelanggan untuk layanan mandiri.</p><a href="{{ route('login') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">Coba Sekarang</a><a href="{{ url('/portal/login') }}" class="mt-3 text-center text-sm font-bold text-teal-700 transition hover:text-teal-900">Masuk ke Client Portal</a></div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-slate-800"><x-application-logo class="h-7 w-7" /> Aksi CRM</a>
                <div class="flex flex-wrap gap-x-5 gap-y-2 font-semibold"><a href="#fitur" class="transition hover:text-teal-700">Fitur</a><a href="{{ route('login') }}" class="transition hover:text-teal-700">Login staf</a><a href="{{ url('/portal/login') }}" class="transition hover:text-teal-700">Client Portal</a></div>
                <p>© {{ now()->year }} Aksisoft. CRM yang fokus pada aksi.</p>
            </div>
        </footer>
    </div>
</body>
</html>
