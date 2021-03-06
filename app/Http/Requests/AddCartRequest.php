<?php

namespace App\Http\Requests;

use App\Models\Product;

class AddCartRequest extends Request
{
    public function rules()
    {
        return [
            'product_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$product = Product::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if (!$product->on_sale) {
                        return $fail('该商品未上架');
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => '请选择商品'
        ];
    }
}
