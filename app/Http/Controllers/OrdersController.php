<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {

        // 创建一个查询构造器
        $builder = Order::query();
        // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
        // search 参数用来模糊搜索商品
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('no', 'like', $like)
                    ->orWhere('address->address', 'like', $like)
                    ->orWhere('address->contact_name', 'like', $like)
                    ->orWhere('address->contact_phone', 'like', $like)
                    ->orWhere('address->zip', 'like', $like)
                    ->orWhere('remark', 'like', $like)
                    ->orWhere('refund_no', 'like', $like)
                    ->orWhere('ship_data->express_company', 'like', $like)
                    ->orWhere('ship_data->express_no', 'like', $like)
                    ->orWhereHas('items.product', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('type', 'like', $like)
                            ->orWhere('stock', 'like', $like)
                            ->orWhere('location', 'like', $like);
                    });
            });
        }
        if ($sort = $request->input('sort', '')) {

            if($sort == 'order_out')
            {
                $builder->where('is_out', true);
            }

            if($sort == 'order_in')
            {
                $builder->where('is_out', false);
            }

        }
        $orders = $builder
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('orders.index', [
            'orders' => $orders,
            'filters'  => [
                'search' => $search,
                'sort'  => $sort,
            ],
        ]);
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $request->input('is_out'));
    }

    public function show(Order $order, Request $request)
    {
        // $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.product'])]);
        // dd($order);
    }


    public function close(Order $order, Request $request)
    {
        if(!$order->closed){
            $order->update(['closed' => true]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 库存中去
            if($order->is_out) {
                foreach ($order->items as $item) {
                    $item->product->addStock($item->amount);
                }
            } else {
                foreach ($order->items as $item) {
                    $item->product->decreaseStock($item->amount);
                }
            }
        }
    }

    public function restore(Order $order, Request $request)
    {
        if($order->closed){
            $order->update(['closed' => false]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到库存中去
            if($order->is_out) {
                foreach ($order->items as $item) {
                    $item->product->decreaseStock($item->amount);
                }
            } else {
                foreach ($order->items as $item) {
                    $item->product->addStock($item->amount);
                }
            }
        }

    }

    public function ship(Order $order, Request $request)
    {

        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }
        // Laravel 5.5 之后 validate 方法可以返回校验过的值
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
            // 因此这里可以直接把数组传过去
            'ship_data'   => $data,
        ]);

    }

    public function refund(Order $order, Request $request)
    {
        if ($order->ship_status === Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单未发货');
        }

        if ($order->refund_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已退货');
        }

        if ($order->closed == true) {
            throw new InvalidRequestException('该订单已关闭');
        }

        $refundNo = Order::getAvailableRefundNo();

        $order->update([
            'refund_no' => $refundNo,
            'refund_status' => Order::REFUND_STATUS_PROCESSING,
        ]);

    }


    public function refund_success(Order $order, Request $request)
    {
        if ($order->ship_status === Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单未发货');
        }

        if ($order->refund_status === Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单无退货编号');
        }

        if ($order->refund_status === Order::REFUND_STATUS_SUCCESS) {
            throw new InvalidRequestException('该订单已退货');
        }

        if ($order->closed == true) {
            throw new InvalidRequestException('该订单已关闭');
        }

        $order->update([
            'refund_status' => Order::REFUND_STATUS_SUCCESS,
        ]);

        if($order->is_out) {
            foreach ($order->items as $item) {
                $item->product->addStock($item->amount);
            }
        } else {
            foreach ($order->items as $item) {
                $item->product->decreaseStock($item->amount);
            }
        }

    }

     public function refund_fail(Order $order, Request $request)
    {
        if ($order->ship_status === Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单未发货');
        }

        if ($order->refund_status === Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单无退货编号');
        }

        if ($order->refund_status === Order::REFUND_STATUS_SUCCESS) {
            throw new InvalidRequestException('该订单已退货');
        }

        if ($order->closed == true) {
            throw new InvalidRequestException('该订单已关闭');
        }

        $order->update([
            'refund_status' => Order::REFUND_STATUS_FAILED,
        ]);

    }

    public function received(Order $order, Request $request)
    {
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('该订单未送货');
        }
        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED,
        ]);
    }


}
