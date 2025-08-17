<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,'.$this->product,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'warehouses' => 'required|array|min:1',
            'warehouses.*.id' => 'required|exists:warehouses,id',
            'warehouses.*.quantity' => 'nullable|integer|min:0',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
