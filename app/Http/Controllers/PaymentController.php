<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($request, $invoice): void {
            $data = $request->validated();
            $method = isset($data['payment_method_id']) ? PaymentMethod::query()->where('is_active', true)->find($data['payment_method_id']) : null;
            $data['payment_method_id'] = $method?->id;
            $data['payment_mode'] = $method?->name ?? ($data['payment_mode'] ?: 'Manual');
            $payment = $invoice->payments()->create(array_merge($data, [
                'recorded_by' => $request->user()->id,
            ]));

            $paidAmount = (float) $invoice->payments()->sum('amount');
            $invoice->paid_amount = $paidAmount;
            $invoice->status = $paidAmount >= (float) $invoice->total
                ? 'paid'
                : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $invoice->save();
        });

        ActivityLogger::record($request->user(), $invoice->client, 'payment.recorded', 'Pembayaran invoice '.$invoice->number.' dicatat.', ['invoice_id' => $invoice->id]);

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

        ActivityLogger::record($request->user(), $invoice->client, 'payment.voided', 'Pembayaran invoice '.$invoice->number.' dibatalkan.', ['invoice_id' => $invoice->id]);

        return to_route('invoices.show', $invoice)->with('status', 'Pembayaran dibatalkan.');
    }
}
