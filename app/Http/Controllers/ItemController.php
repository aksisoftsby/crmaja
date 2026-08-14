<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Item::class);

        $items = Item::query()
            ->when($request->string('search')->trim()->value(), fn ($query, string $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->string('status')->value() === 'active'))
            ->latest()->paginate(15)->withQueryString();

        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        return view('items.create');
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        Item::create($request->validated());

        return to_route('items.index')->with('status', 'Item berhasil ditambahkan.');
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return to_route('items.index')->with('status', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);
        $item->delete();

        return to_route('items.index')->with('status', 'Item berhasil dihapus.');
    }
}
