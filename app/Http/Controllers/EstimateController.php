<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstimateRequest;
use App\Http\Requests\UpdateEstimateRequest;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EstimateController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Estimate::class);

        return view('estimates.index', ['estimates' => Estimate::query()->with('client')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        $this->authorize('create', Estimate::class);

        return view('estimates.create', $this->formOptions());
    }

    public function store(StoreEstimateRequest $request): RedirectResponse
    {
        $estimate = DB::transaction(function () use ($request): Estimate {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $data['number'] = 'EST-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $data['status'] = 'draft';
            $data['created_by'] = $request->user()->id;

            return $this->persist(new Estimate, $data, $items);
        });

        return to_route('estimates.show', $estimate)->with('status', 'Estimate berhasil dibuat.');
    }

    public function show(Estimate $estimate): View
    {
        $this->authorize('view', $estimate);
        $estimate->load(['client', 'items.item']);

        return view('estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate): View
    {
        $this->authorize('update', $estimate);
        $estimate->load('items');

        return view('estimates.edit', array_merge(['estimate' => $estimate], $this->formOptions()));
    }

    public function update(UpdateEstimateRequest $request, Estimate $estimate): RedirectResponse
    {
        DB::transaction(function () use ($request, $estimate): void {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $this->persist($estimate, $data, $items);
        });

        return to_route('estimates.show', $estimate)->with('status', 'Estimate berhasil diperbarui.');
    }

    public function destroy(Estimate $estimate): RedirectResponse
    {
        $this->authorize('delete', $estimate);
        $estimate->delete();

        return to_route('estimates.index')->with('status', 'Estimate telah diarsipkan.');
    }

    private function persist(Estimate $estimate, array $data, array $items): Estimate
    {
        $subtotal = collect($items)->sum(fn (array $item): float => (float) $item['qty'] * (float) $item['rate']);
        $data['subtotal'] = $subtotal;
        $data['discount'] = (float) ($data['discount'] ?? 0);
        $data['total'] = max(0, $subtotal - $data['discount']);
        $estimate->fill($data);
        $estimate->save();
        $estimate->items()->delete();
        foreach ($items as $item) {
            $estimate->items()->create(['item_id' => $item['item_id'] ?? null, 'description' => $item['description'], 'qty' => $item['qty'], 'rate' => $item['rate'], 'amount' => (float) $item['qty'] * (float) $item['rate']]);
        }

return $estimate;
    }

    private function formOptions(): array
    {
        return ['clients' => Client::query()->active()->orderBy('company_name')->get(), 'items' => Item::query()->where('is_active',true)->orderBy('title')->get()];
    }
}
