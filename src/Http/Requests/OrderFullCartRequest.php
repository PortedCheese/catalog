<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderFullCartRequest extends FormRequest
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
            'name' => 'required|min:2',
            'email' => 'nullable|required_without:phone|email',
            'phone' => 'required_without:email',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Имя',
            'email' => "E-mail",
            'phone' => 'Телефон',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле :attribute обязательно для заполнения',
            'name.min' => "Поле :attribute должно быть минимум :min символа",
            'email.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            'email.email' => "Поле :attribute должно быть валидным e-mail адресом",
            'phone.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
        ];
    }
}
