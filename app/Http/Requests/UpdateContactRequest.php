<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contact = $this->route('contact');

        return $contact instanceof Contact && ($this->user()?->can('update', $contact->client) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', 'required_with:password'],
            'phone' => ['nullable', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
