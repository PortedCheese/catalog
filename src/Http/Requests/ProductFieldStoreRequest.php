<?php

namespace PortedCheese\Catalog\Http\Requests;

use App\ProductField;
use Illuminate\Foundation\Http\FormRequest;

class ProductFieldStoreRequest extends FormRequest
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
        return ProductField::requestProductFieldStoreRules();
    }

    public function attributes()
    {
        return ProductField::requestProductFieldStoreAttributes();
    }
}
