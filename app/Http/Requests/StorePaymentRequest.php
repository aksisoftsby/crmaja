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
            'payment_mode' => ['nullable', 'string', 'max:100'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
