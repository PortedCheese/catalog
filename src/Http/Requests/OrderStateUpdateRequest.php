<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStateUpdateRequest extends FormRequest
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
        $state = $this->route()->parameter('state', NULL);
        $id = !empty($state) ? $state->id : NULL;
        return [
            'title' => "required|min:2|unique:order_states,title,{$id}",
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Заголовок',
        ];
    }
}
