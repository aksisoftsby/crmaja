<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Client;
use App\Models\Item;
use App\Models\Lead;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Proposal::class);
        $proposals = Proposal::query()->with(['client', 'lead', 'creator'])->latest()->paginate(15);

        return view('proposals.index', compact('proposals'));
    }

    public function create(): View
    {
        $this->authorize('create', Proposal::class);

        return view('proposals.create', $this->formOptions());
    }

    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $proposal = DB::transaction(function () use ($request): Proposal {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $data['number'] = 'PR-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $data['status'] = 'draft';
            $data['created_by'] = $request->user()->id;

            return $this->persistProposal(new Proposal, $data, $items);
        });

        return to_route('proposals.show', $proposal)->with('status', 'Proposal berhasil dibuat.');
    }

    public function show(Proposal $proposal): View
    {
        $this->authorize('view', $proposal);
        $proposal->load(['client', 'lead', 'creator', 'items.item']);

        return view('proposals.show', compact('proposal'));
    }

    public function edit(Proposal $proposal): View
    {
        $this->authorize('update', $proposal);
        $proposal->load('items');

        return view('proposals.edit', array_merge(['proposal' => $proposal], $this->formOptions()));
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        DB::transaction(function () use ($request, $proposal): void {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $this->persistProposal($proposal, $data, $items);
        });

        return to_route('proposals.show', $proposal)->with('status', 'Proposal berhasil diperbarui.');
    }

    public function destroy(Proposal $proposal): RedirectResponse
    {
        $this->authorize('delete', $proposal);
        $proposal->delete();

        return to_route('proposals.index')->with('status', 'Proposal telah diarsipkan.');
    }

    private function persistProposal(Proposal $proposal, array $data, array $items): Proposal
    {
        $subtotal = collect($items)->sum(fn (array $item): float => (float) $item['qty'] * (float) $item['rate']);
        $data['subtotal'] = $subtotal;
        $data['discount'] = (float) ($data['discount'] ?? 0);
        $data['total'] = max(0, $subtotal - $data['discount']);
        $proposal->fill($data);
        $proposal->save();
        $proposal->items()->delete();
        foreach ($items as $item) {
            $proposal->items()->create(['item_id' => $item['item_id'] ?? null, 'description' => $item['description'], 'qty' => $item['qty'], 'rate' => $item['rate'], 'amount' => (float) $item['qty'] * (float) $item['rate']]);
        }

        return $proposal;
    }

    private function formOptions(): array
    {
        return ['clients' => Client::query()->active()->orderBy('company_name')->get(), 'leads' => Lead::query()->where('is_converted', false)->orderBy('name')->get(), 'items' => Item::query()->where('is_active', true)->orderBy('title')->get()];
    }
}
