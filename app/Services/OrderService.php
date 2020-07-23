<?php

namespace App\Services;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items, $is_out)
    {

        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $remark, $items, $is_out) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order   = new Order([
                'address'      => [ // 将地址信息放入订单中
                                    'address'       => $address->full_address,
                                    'zip'           => $address->zip,
                                    'contact_name'  => $address->contact_name,
                                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'is_out'       => $is_out,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $product  = Product::find($data['product_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                ]);
                $item->product()->associate($product->id);
                $item->save();
                if($order->is_out) {
                    if ($product->decreaseStock($data['amount']) <= 0) {
                        throw new InvalidRequestException('该商品库存不足');
                    }
                } else {
                    $product->addStock($data['amount']);
                }

            }



            // 将下单的商品从购物车中移除
            $productIds = collect($items)->pluck('product_id')->all();
            app(CartService::class)->remove($productIds);

            return $order;
        });

        // 这里我们直接使用 dispatch 函数
        // dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}
