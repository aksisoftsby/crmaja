<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $client = $this->route('client');

        return $client instanceof Client && ($this->user()?->can('update', $client) ?? false);
    }

    public function rules(): array
    {
        return ['content' => ['required', 'string', 'max:10000']];
    }
}
