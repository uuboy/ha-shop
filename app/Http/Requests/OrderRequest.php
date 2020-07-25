<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Product;

class OrderRequest extends Request
{
    public function rules()
    {
        return [
            // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            // 后面这个条件非常重要，否则恶意用户可以用不同的地址 ID 不断提交订单来遍历出平台所有用户的收货地址
            'address_id'     => [
                'required',
            ],
            'items'  => ['required', 'array'],
            'items.*.product_id' => [ // 检查 items 数组下每一个子数组的 sku_id 参数
                                  'required',
                                  function ($attribute, $value, $fail) {
                                      if (!$product = Product::find($value)) {
                                          return $fail('该商品不存在');
                                      }
                                      if (!$product->on_sale) {
                                          return $fail('该商品未上架');
                                      }

                                      // 获取当前索引
                                      preg_match('/items\.(\d+)\.product_id/', $attribute, $m);
                                      $index = $m[1];
                                  },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];

    }

    public function messages()
    {
        return [
            'address_id.required' => '请选择送货单位'
        ];
    }
}
