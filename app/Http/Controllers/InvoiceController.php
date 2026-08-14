<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        return view('invoices.index', [
            'invoices' => Invoice::query()->with('client')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Invoice::class);

        return view('invoices.create', $this->formOptions());
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = DB::transaction(function () use ($request): Invoice {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $data['number'] = 'INV-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $data['status'] = 'unpaid';
            $data['created_by'] = $request->user()->id;

            return $this->persist(new Invoice, $data, $items);
        });

        return to_route('invoices.show', $invoice)->with('status', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);
        $invoice->load(['client', 'items.item', 'payments.recorder']);

        return view('invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['client', 'items.item', 'payments.recorder']);

        return Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4')
            ->download($invoice->number.'.pdf');
    }

    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);
        $invoice->load('items');

        return view('invoices.edit', array_merge(['invoice' => $invoice], $this->formOptions()));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($request, $invoice): void {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $this->persist($invoice, $data, $items);
        });

        return to_route('invoices.show', $invoice)->with('status', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return to_route('invoices.index')->with('status', 'Invoice telah diarsipkan.');
    }

    private function persist(Invoice $invoice, array $data, array $items): Invoice
    {
        $subtotal = collect($items)->sum(fn (array $item): float => (float) $item['qty'] * (float) $item['rate']);
        $data['subtotal'] = $subtotal;
        $data['discount'] = (float) ($data['discount'] ?? 0);
        $data['total'] = max(0, $subtotal - $data['discount']);
        $invoice->fill($data);
        $invoice->save();
        $invoice->items()->delete();

        foreach ($items as $item) {
            $invoice->items()->create([
                'item_id' => $item['item_id'] ?? null,
                'description' => $item['description'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'amount' => (float) $item['qty'] * (float) $item['rate'],
            ]);
        }

        return $invoice;
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::query()->active()->orderBy('company_name')->get(),
            'items' => Item::query()->where('is_active', true)->orderBy('title')->get(),
        ];
    }
}
