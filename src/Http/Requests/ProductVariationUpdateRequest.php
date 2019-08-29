<?php

namespace PortedCheese\Catalog\Http\Requests;

use App\ProductVariation;
use Illuminate\Foundation\Http\FormRequest;

class ProductVariationUpdateRequest extends FormRequest
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
        return ProductVariation::requestProductVariationUpdateRules($this);
    }

    public function attributes()
    {
        return ProductVariation::requestProductVariationUpdateAttributes();
    }
}
