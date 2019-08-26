<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateCategoryRequest extends FormRequest
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
            'category_id' => "required|numeric|exists:categories,id",
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => 'Категория',
        ];
    }
}
