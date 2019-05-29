<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeQuantityRequest extends FormRequest
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
            'quantity' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'quantity.required' => 'Количество не может быть пустым',
            'quantity.numeric' => 'Количество должно быть числом',
            'quantity.min' => "Количество должно быть минимум :min",
        ];
    }
}
