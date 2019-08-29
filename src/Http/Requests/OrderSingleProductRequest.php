<?php

namespace PortedCheese\Catalog\Http\Requests;

use App\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderSingleProductRequest extends FormRequest
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
        return Order::requestOrderSingleProductRules();
    }

    public function attributes()
    {
        return Order::requestOrderSingleProductAttributes();
    }

    public function messages()
    {
        return Order::requestOrderSingleProductMessages();
    }
}
