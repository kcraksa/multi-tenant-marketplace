<?php

declare(strict_types=1);

namespace App\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_per_item' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku,' . $productId],
            'barcode' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'track_inventory' => ['boolean'],
            'continue_selling' => ['boolean'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['nullable', 'string', 'in:kg,g,lb,oz'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'file', 'image', 'max:5120'], // 5MB max
            'status' => ['nullable', 'string', 'in:active,draft,archived'],
            'featured' => ['boolean'],
            'category_id' => ['nullable', 'integer'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
