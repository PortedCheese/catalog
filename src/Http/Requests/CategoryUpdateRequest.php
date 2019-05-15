<?php

namespace PortedCheese\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
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
        $category = $this->route()->parameter('category', NULL);
        $id = !empty($category) ? $category->id : NULL;
        return [
            'title' => "required|min:2|unique:categories,title,{$id}",
            'slug' => "min:2|unique:categories,slug,{$id}",
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
