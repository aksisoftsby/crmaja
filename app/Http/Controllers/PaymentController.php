<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($request, $invoice): void {
            $payment = $invoice->payments()->create(array_merge($request->validated(), [
                'recorded_by' => $request->user()->id,
            ]));

            $paidAmount = (float) $invoice->payments()->sum('amount');
            $invoice->paid_amount = $paidAmount;
            $invoice->status = $paidAmount >= (float) $invoice->total
                ? 'paid'
                : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $invoice->save();
        });

        return to_route('invoices.show', $invoice)->with('status', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Invoice $invoice, int $payment): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $record = $invoice->payments()->findOrFail($payment);

        DB::transaction(function () use ($invoice, $record): void {
            $record->delete();
            $paidAmount = (float) $invoice->payments()->sum('amount');
            $invoice->paid_amount = $paidAmount;
            $invoice->status = $paidAmount >= (float) $invoice->total
                ? 'paid'
                : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $invoice->save();
        });

        return to_route('invoices.show', $invoice)->with('status', 'Pembayaran dibatalkan.');
    }
}
