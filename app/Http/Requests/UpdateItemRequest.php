<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');

        return $item instanceof Item && ($this->user()?->can('update', $item) ?? false);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'rate' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
