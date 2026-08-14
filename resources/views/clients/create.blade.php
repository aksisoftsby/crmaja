<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah pelanggan</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('clients.store') }}">
                    @include('clients.partials.form', ['submitLabel' => 'Simpan pelanggan'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
