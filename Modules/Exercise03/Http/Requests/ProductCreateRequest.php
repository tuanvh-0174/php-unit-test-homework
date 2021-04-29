<?php

namespace Modules\Exercise03\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Exercise03\Models\Product;

class ProductCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
            //'sku' => ['required', Rule::unique(Product::getTableName(), 'sku')],
            'image' => ['nullable', 'mimes:jpg,png'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['required'],
            'short_description' => ['nullable', 'max:255'],
        ];
    }
}
