<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFieldUpdateRequest extends FormRequest
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
            'value' => 'required|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'value' => 'Значение',
        ];
    }
}
