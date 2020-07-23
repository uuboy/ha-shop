@extends('layouts.app')
@section('title', '订单列表')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">订单列表</div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($orders as $order)
                        <li class="list-group-item">
                            <div class="card">
                                <div class="card-header">
                                    订单号：{{ $order->no }}
                                    <span class="ml-4">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                    <span class="float-right">单据类型：@if($order->is_out)出库清单@else入库清单@endif</span>

                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>货品信息</th>
                                            <th class="text-center">数量</th>
                                            <th class="text-center">状态</th>
                                            <th class="text-center">操作</th>
                                        </tr>
                                        </thead>
                                        @foreach($order->items as $index => $item)
                                            <tr data-id="{{ $order->id }}">
                                                <td class="product-info">
                                                        <span class="product-title">
                                                           <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                                        </span>
                                                        <span class="sku-title">{{ $item->product->type }}</span>
                                                </td>
                                                <td class="sku-amount text-center">{{ $item->amount }}</td>
                                                @if($index === 0)
                                                    <td rowspan="{{ count($order->items) }}" class="text-center">
                                                        @if($order->closed)
                                                            已退回
                                                        @else
                                                            @if($order->is_out)
                                                                已出库
                                                            @else
                                                                已入库
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td rowspan="{{ count($order->items) }}" class="text-center">
                                                        <a class="btn btn-primary btn-sm" href="{{ route('orders.show', ['order' => $order->id]) }}">查看订单</a>
                                                        @if($order->closed)
                                                            <button class="btn btn-success btn-sm btn-restore">订单恢复</button>
                                                        @else
                                                            <button class="btn btn-danger btn-sm btn-remove">订单退回</button>
                                                        @endif

                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="float-right">{{ $orders->render() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
    $(document).ready(function () {
        // 监听 移除 按钮的点击事件
        $('.btn-remove').click(function () {
            // $(this) 可以获取到当前点击的 移除 按钮的 jQuery 对象
            // closest() 方法可以获取到匹配选择器的第一个祖先元素，在这里就是当前点击的 移除 按钮之上的 <tr> 标签
            // data('id') 方法可以获取到我们之前设置的 data-id 属性的值，也就是对应的 SKU id
            var id = $(this).closest('tr').data('id');
            swal({
                title: "确认要将该订单退回？",
                icon: "warning",
                buttons: ['取消', '确定'],
                dangerMode: true,
            })
                .then(function(willDelete) {
                    // 用户点击 确定 按钮，willDelete 的值就会是 true，否则为 false
                    if (!willDelete) {
                        return;
                    }
                    axios.post('/orders/' + id + '/close')
                        .then(function () {
                            location.reload();
                        })
                });
        });

        $('.btn-restore').click(function () {
            // $(this) 可以获取到当前点击的 移除 按钮的 jQuery 对象
            // closest() 方法可以获取到匹配选择器的第一个祖先元素，在这里就是当前点击的 移除 按钮之上的 <tr> 标签
            // data('id') 方法可以获取到我们之前设置的 data-id 属性的值，也就是对应的 SKU id
            var id = $(this).closest('tr').data('id');
            swal({
                title: "确认要将该订单恢复？",
                icon: "warning",
                buttons: ['取消', '确定'],
                dangerMode: true,
            })
                .then(function(willDelete) {
                    // 用户点击 确定 按钮，willDelete 的值就会是 true，否则为 false
                    if (!willDelete) {
                        return;
                    }
                    axios.post('/orders/' + id + '/restore')
                        .then(function () {
                            location.reload();
                        })
                });
        });
    });
</script>
@endsection
