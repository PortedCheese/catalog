<?php

namespace PortedCheese\Catalog\Http\Requests;

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
        $product = $this->route()->parameter('product', NULL);
        $id = !empty($product) ? $product->id : NULL;
        return [
            'title' => "required|min:2|unique:products,title,{$id}",
            'slug' => "nullable|min:2|unique:products,slug,{$id}",
            'main_image' => 'nullable|image',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Заголовок',
            'main_image' => 'Главное изображение',
        ];
    }
}
