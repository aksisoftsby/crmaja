<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $client = $this->route('client');

        return $client instanceof Client && ($this->user()?->can('update', $client) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:4000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:32'],
            'country' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'size:3'],
            'customer_group_id' => ['nullable', 'integer', 'exists:customer_groups,id'],
            'assigned_staff_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
