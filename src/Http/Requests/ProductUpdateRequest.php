<?php

namespace PortedCheese\Catalog\Http\Requests;

use App\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
        return Product::requestProductUpdateRules($this);
    }

    public function attributes()
    {
        return Product::requestProductUpdateAttributes();
    }
}
