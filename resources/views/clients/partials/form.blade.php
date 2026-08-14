@csrf

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="company_name" value="Nama perusahaan" />
        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $client->company_name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="vat_number" value="NPWP / VAT" />
        <x-text-input id="vat_number" name="vat_number" type="text" class="mt-1 block w-full" :value="old('vat_number', $client->vat_number ?? '')" />
        <x-input-error :messages="$errors->get('vat_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" value="Telepon" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $client->phone ?? '')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="website" value="Website" />
        <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $client->website ?? '')" placeholder="https://contoh.co.id" />
        <x-input-error :messages="$errors->get('website')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="currency" value="Mata uang" />
        <x-text-input id="currency" name="currency" type="text" maxlength="3" class="mt-1 block w-full uppercase" :value="old('currency', $client->currency ?? 'IDR')" required />
        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="customer_group_id" value="Grup pelanggan" />
        <select id="customer_group_id" name="customer_group_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tanpa grup</option>
            @foreach ($groups as $group)
                <option value="{{ $group->id }}" @selected((string) old('customer_group_id', $client->customer_group_id ?? '') === (string) $group->id)>{{ $group->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('customer_group_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="assigned_staff_id" value="Staf penanggung jawab" />
        <select id="assigned_staff_id" name="assigned_staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Belum ditugaskan</option>
            @foreach ($staff as $member)
                <option value="{{ $member->id }}" @selected((string) old('assigned_staff_id', $client->assigned_staff_id ?? '') === (string) $member->id)>{{ $member->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('assigned_staff_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="address" value="Alamat" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $client->address ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="city" value="Kota" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $client->city ?? '')" />
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="state" value="Provinsi" />
            <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $client->state ?? '')" />
            <x-input-error :messages="$errors->get('state')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="zip" value="Kode pos" />
            <x-text-input id="zip" name="zip" type="text" class="mt-1 block w-full" :value="old('zip', $client->zip ?? '')" />
            <x-input-error :messages="$errors->get('zip')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="country" value="Negara" />
            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $client->country ?? 'Indonesia')" />
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $client->is_active ?? true))>
            Pelanggan aktif
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ isset($client) ? route('clients.show', $client) : route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Batal</a>
</div>
