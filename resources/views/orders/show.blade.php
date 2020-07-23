@extends('layouts.app')
@section('title', '查看订单')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                <h4>单据详情</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>货品信息</th>
                        <th class="text-center">数量</th>
                    </tr>
                    </thead>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td class="product-info">
                                <div>
                                  <span class="product-title">
                                     <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                  </span>
                                    <span class="sku-title">{{ $item->product->type }}</span>
                                </div>
                            </td>
                            <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                        </tr>
                    @endforeach
                    <tr><td colspan="2"></td></tr>
                </table>
                <div class="order-bottom">
                    <div class="order-info">
                        <div class="line"><div class="line-label">收货地址：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
                        <div class="line"><div class="line-label">订单备注：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
                        <div class="line"><div class="line-label">订单编号：</div><div class="line-value">{{ $order->no }}</div></div>
                    </div>
                    <div class="order-summary text-right">

                        <div>
                            <div class="total-amount">
                              <span>单据类型：</span>
                              @if($order->is_out)
                                <div class="value">出库清单</div>
                              @else
                                <div class="value">入库清单</div>
                              @endif
                            </div>
                            <span>物流状态：</span>
                            <div class="value">
                                @if($order->closed)
                                    已退回
                                @else
                                    已发货
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
    $(document).ready(function() {

    });
</script>
@endsection
