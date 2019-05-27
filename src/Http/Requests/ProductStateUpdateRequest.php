<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStateUpdateRequest extends FormRequest
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
            'title' => "required|min:2|unique:product_states,title,{$id}",
            'slug' => "nullable|min:2|unique:product_states,slug,{$id}",
            'color' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Заголовок',
            'color' => 'Цвет',
        ];
    }
}
