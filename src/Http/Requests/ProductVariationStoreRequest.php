<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku' => 'required|min:2|unique:product_variations,sku',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'sku' => 'Артикул',
            'product_id' => "Товар",
            'price' => 'Цена',
            'sale_price' => 'Цена со скидкой',
        ];
    }
}
