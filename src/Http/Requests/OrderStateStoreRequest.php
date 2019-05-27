<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStateStoreRequest extends FormRequest
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
            'title' => 'required|min:2|unique:order_states,title',
            'machine' => 'nullable|min:2|unique:order_states,machine',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Заголовок',
            'machine' => 'Ключ',
        ];
    }
}
