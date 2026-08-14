<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Pelanggan</h2>
            @can('create', App\Models\Client::class)
                <a href="{{ route('clients.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tambah pelanggan</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Cari pelanggan" />
                        <x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="request('search')" placeholder="Nama perusahaan, telepon, atau kota" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua status</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Perusahaan</th>
                                <th class="px-6 py-3">Kontak utama</th>
                                <th class="px-6 py-3">Staf</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                            @forelse ($clients as $client)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('clients.show', $client) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">{{ $client->company_name }}</a>
                                        <div class="mt-1 text-xs text-gray-500">{{ collect([$client->city, $client->country])->filter()->join(', ') ?: 'Lokasi belum diisi' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($client->primaryContact)
                                            <div>{{ $client->primaryContact->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $client->primaryContact->email ?: $client->primaryContact->phone }}</div>
                                        @else
                                            <span class="text-gray-400">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $client->assignedStaff?->name ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <span @class(['inline-flex rounded-full px-2 py-1 text-xs font-medium', 'bg-emerald-100 text-emerald-800' => $client->is_active, 'bg-gray-100 text-gray-700' => ! $client->is_active])>{{ $client->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('clients.show', $client) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada pelanggan yang sesuai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-4">{{ $clients->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
