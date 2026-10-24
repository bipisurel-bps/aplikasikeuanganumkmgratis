<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'date_format:Y-m'],
            'budgets' => ['required', 'array'],
            'budgets.*' => ['nullable', 'integer', 'min:0', 'max:1000000000000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $allowed = config('umkm.expense_categories');
            foreach (array_keys($this->input('budgets', [])) as $category) {
                if (! in_array($category, $allowed, true)) {
                    $v->errors()->add('budgets', "Kategori tidak dikenal: {$category}");
                }
            }
        });
    }
}
