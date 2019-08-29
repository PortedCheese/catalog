<?php

namespace PortedCheese\Catalog\Http\Requests;

use App\OrderState;
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
        return OrderState::requestOrderStateUpdateRules($this);
    }

    public function attributes()
    {
        return OrderState::requestOrderStateUpdateAttributes();
    }
}
