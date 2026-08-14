<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Tambah item</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-4xl sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow-sm"><form method="POST" action="{{ route('items.store') }}">@include('items.partials.form', ['submitLabel' => 'Simpan item'])</form></div></div></div>
</x-app-layout>
