<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Ubah lead: {{ $lead->name }}</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-5xl sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow-sm"><form method="POST" action="{{ route('leads.update', $lead) }}">@method('PUT')@include('leads.partials.form', ['submitLabel' => 'Simpan perubahan'])</form></div></div></div>
</x-app-layout>
