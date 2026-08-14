@csrf
<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="md:col-span-2"><x-input-label for="title" value="Nama item" /><x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title ?? '')" required autofocus /><x-input-error :messages="$errors->get('title')" class="mt-2" /></div>
    <div><x-input-label for="rate" value="Harga / tarif" /><x-text-input id="rate" name="rate" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('rate', $item->rate ?? 0)" required /><x-input-error :messages="$errors->get('rate')" class="mt-2" /></div>
    <div><x-input-label for="unit" value="Satuan" /><x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', $item->unit ?? 'unit')" required /><x-input-error :messages="$errors->get('unit')" class="mt-2" /></div>
    <div class="md:col-span-2"><x-input-label for="description" value="Deskripsi" /><textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $item->description ?? '') }}</textarea><x-input-error :messages="$errors->get('description')" class="mt-2" /></div>
    <div><label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_active', $item->is_active ?? true))> Item aktif</label></div>
</div>
<div class="mt-6 flex gap-3"><x-primary-button>{{ $submitLabel }}</x-primary-button><a href="{{ route('items.index') }}" class="text-sm text-gray-600">Batal</a></div>
