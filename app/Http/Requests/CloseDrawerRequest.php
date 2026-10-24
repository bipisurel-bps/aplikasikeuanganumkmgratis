<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseDrawerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'closing_cash' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
