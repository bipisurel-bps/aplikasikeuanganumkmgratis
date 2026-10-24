<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenDrawerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['opening_cash' => ['required', 'integer', 'min:0', 'max:1000000000']];
    }
}
