<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return $invoice instanceof Invoice && ($this->user()?->can('update', $invoice) ?? false);
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'paid_at' => ['required', 'date'],
            'payment_mode' => ['required', 'string', 'max:100'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
