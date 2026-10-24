<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:150'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000000'],
            'min_stock' => ['required', 'integer', 'min:0', 'max:10000000'],
            'cost_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'sell_price' => ['required', 'integer', 'min:0', 'max:1000000000', 'gte:cost_price'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ['sell_price.gte' => 'Harga jual tidak boleh lebih rendah dari harga beli.'];
    }
}
