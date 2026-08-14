<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Client Portal' }} — Aksi CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4">
            <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2.5" aria-label="Aksi CRM Client Portal">
                <x-application-logo class="h-9 w-9" />
                <span class="text-base font-extrabold tracking-tight text-slate-900">Aksi<span class="text-teal-700">CRM</span><span class="ml-1.5 text-xs font-semibold text-slate-400">Client Portal</span></span>
            </a>
            <div class="hidden gap-4 text-sm font-semibold md:flex">
                <a href="{{ route('portal.invoices') }}" class="text-slate-600 transition hover:text-teal-700">Invoice</a>
                <a href="{{ route('portal.proposals') }}" class="text-slate-600 transition hover:text-teal-700">Proposal</a>
                <a href="{{ route('portal.estimates') }}" class="text-slate-600 transition hover:text-teal-700">Estimate</a>
                <a href="{{ route('portal.projects') }}" class="text-slate-600 transition hover:text-teal-700">Project</a>
                <a href="{{ route('portal.tickets') }}" class="text-slate-600 transition hover:text-teal-700">Ticket</a>
                <a href="{{ route('portal.knowledge-base') }}" class="text-slate-600 transition hover:text-teal-700">Knowledge Base</a>
            </div>
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <button class="text-sm font-semibold text-slate-600 transition hover:text-teal-700">Keluar</button>
            </form>
        </div>
    </nav>
    <main class="py-8">
        @if(session('status'))
            <div class="mx-auto mb-5 max-w-7xl rounded-xl bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
