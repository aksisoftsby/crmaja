<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $client->company_name }}</h2>
                <p class="mt-1 text-sm text-gray-500">Detail pelanggan dan PIC</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Kembali</a>
                @can('update', $client)
                    <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500">Ubah</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <section class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h3 class="font-semibold text-gray-900">Profil perusahaan</h3>
                        <span @class(['inline-flex rounded-full px-2 py-1 text-xs font-medium', 'bg-emerald-100 text-emerald-800' => $client->is_active, 'bg-gray-100 text-gray-700' => ! $client->is_active])>{{ $client->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <dl class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 text-sm sm:grid-cols-2">
                        <div><dt class="text-gray-500">Telepon</dt><dd class="mt-1 font-medium text-gray-900">{{ $client->phone ?: '—' }}</dd></div>
                        <div><dt class="text-gray-500">Website</dt><dd class="mt-1 font-medium text-gray-900">@if ($client->website)<a class="text-indigo-600 hover:underline" href="{{ $client->website }}" target="_blank" rel="noopener">{{ $client->website }}</a>@else — @endif</dd></div>
                        <div><dt class="text-gray-500">NPWP / VAT</dt><dd class="mt-1 font-medium text-gray-900">{{ $client->vat_number ?: '—' }}</dd></div>
                        <div><dt class="text-gray-500">Mata uang</dt><dd class="mt-1 font-medium text-gray-900">{{ $client->currency }}</dd></div>
                        <div><dt class="text-gray-500">Grup pelanggan</dt><dd class="mt-1 font-medium text-gray-900">{{ $client->customerGroup?->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Staf penanggung jawab</dt><dd class="mt-1 font-medium text-gray-900">{{ $client->assignedStaff?->name ?? '—' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-gray-500">Alamat</dt><dd class="mt-1 whitespace-pre-line font-medium text-gray-900">{{ $client->address ?: '—' }}{{ $client->city ? "\n{$client->city}" : '' }}{{ $client->state ? ", {$client->state}" : '' }}{{ $client->zip ? " {$client->zip}" : '' }}{{ $client->country ? "\n{$client->country}" : '' }}</dd></div>
                    </dl>
                </section>

                <aside class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900">Informasi sistem</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-gray-500">Dibuat oleh</dt><dd class="font-medium text-gray-900">{{ $client->creator?->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Dibuat pada</dt><dd class="font-medium text-gray-900">{{ $client->created_at->translatedFormat('d M Y H:i') }}</dd></div>
                        <div><dt class="text-gray-500">Diperbarui</dt><dd class="font-medium text-gray-900">{{ $client->updated_at->translatedFormat('d M Y H:i') }}</dd></div>
                    </dl>
                    @can('delete', $client)
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="mt-6 border-t border-gray-100 pt-4" onsubmit="return confirm('Arsipkan pelanggan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm font-medium text-red-600 hover:text-red-800">Arsipkan pelanggan</button>
                        </form>
                    @endcan
                </aside>
            </div>

            <section class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"><h3 class="font-semibold text-gray-900">Catatan internal</h3><div class="mt-4 space-y-3">@forelse($client->notes as $note)<div class="border-b pb-3 last:border-0"><div class="flex items-start justify-between gap-3"><div><p class="whitespace-pre-line text-sm text-gray-700">{{ $note->content }}</p><p class="mt-1 text-xs text-gray-500">{{ $note->creator?->name ?? 'Pengguna terhapus' }} · {{ $note->created_at->format('d M Y H:i') }}</p></div>@can('update', $client)<form method="POST" action="{{ route('clients.notes.destroy', [$client, $note]) }}">@csrf @method('DELETE')<button class="text-xs text-red-600">Hapus</button></form>@endcan</div></div>@empty<p class="text-sm text-gray-500">Belum ada catatan internal.</p>@endforelse</div>@can('update', $client)<form method="POST" action="{{ route('clients.notes.store', $client) }}" class="mt-5 flex gap-3 border-t pt-5">@csrf <textarea name="content" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="Tambahkan catatan internal" required></textarea><x-primary-button>Simpan</x-primary-button></form>@endcan</section>

            <section class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4"><h3 class="font-semibold text-gray-900">Kontak / PIC</h3></div>
                <div class="divide-y divide-gray-200">
                    @forelse ($client->contacts as $contact)
                        <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4">
                            <div>
                                <div class="flex items-center gap-2 font-semibold text-gray-900">{{ $contact->full_name }} @if ($contact->is_primary)<span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">PIC utama</span>@endif @if (! $contact->is_active)<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">Nonaktif</span>@endif</div>
                                <div class="mt-1 text-sm text-gray-500">{{ collect([$contact->title, $contact->email, $contact->phone])->filter()->join(' · ') ?: 'Detail kontak belum diisi' }}</div>
                            </div>
                            @can('update', $client)
                                <form method="POST" action="{{ route('clients.contacts.destroy', [$client, $contact]) }}" onsubmit="return confirm('Arsipkan kontak ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-medium text-red-600 hover:text-red-800">Arsipkan</button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <div class="px-6 py-8 text-sm text-gray-500">Belum ada kontak untuk pelanggan ini.</div>
                    @endforelse
                </div>
            </section>

            @can('update', $client)
                <section class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900">Tambah kontak</h3>
                    <form method="POST" action="{{ route('clients.contacts.store', $client) }}" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                        @csrf
                        <div><x-input-label for="first_name" value="Nama depan" /><x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required /><x-input-error :messages="$errors->get('first_name')" class="mt-2" /></div>
                        <div><x-input-label for="last_name" value="Nama belakang" /><x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" /><x-input-error :messages="$errors->get('last_name')" class="mt-2" /></div>
                        <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                        <div><x-input-label for="phone" value="Telepon" /><x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" /><x-input-error :messages="$errors->get('phone')" class="mt-2" /></div>
                        <div><x-input-label for="title" value="Jabatan" /><x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" /><x-input-error :messages="$errors->get('title')" class="mt-2" /></div>
                        <div><x-input-label for="password" value="Kata sandi Client Portal (opsional)" /><x-text-input id="password" name="password" type="password" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
                        <div><x-input-label for="password_confirmation" value="Konfirmasi kata sandi" /><x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" /></div>
                        <div class="flex items-end gap-5 pb-2"><label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="hidden" name="is_primary" value="0"><input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_primary'))> Jadikan PIC utama</label><label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', true))> Aktif</label></div>
                        <div class="md:col-span-2"><x-primary-button>Simpan kontak</x-primary-button></div>
                    </form>
                </section>
            @endcan
        </div>
    </div>
</x-app-layout>
