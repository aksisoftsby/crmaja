<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lead = $this->route('lead');

        return $lead instanceof Lead && ($this->user()?->can('update', $lead) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source_id' => ['nullable', 'integer', 'exists:lead_sources,id'],
            'status_id' => ['required', 'integer', 'exists:lead_statuses,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'lead_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
