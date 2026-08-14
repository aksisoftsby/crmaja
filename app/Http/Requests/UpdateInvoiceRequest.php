<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return $invoice instanceof Invoice && ($this->user()?->can('update', $invoice) ?? false);
    }

    public function rules(): array
    {
        return ['client_id' => ['required', 'integer', 'exists:clients,id'], 'date' => ['required', 'date'], 'due_date' => ['nullable', 'date', 'after_or_equal:date'], 'status' => ['required', 'in:draft,unpaid,partial,paid,overdue,cancelled'], 'discount' => ['nullable', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:10000'], 'items' => ['required', 'array', 'min:1'], 'items.*.item_id' => ['nullable', 'integer', 'exists:items,id'], 'items.*.description' => ['required', 'string', 'max:5000'], 'items.*.qty' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0']];
    }
}
