@extends('layouts.app')
@section('title', '订单列表')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">出入库流水单</div>
            <div class="card-body">
                <!-- 筛选组件开始 -->
                <form action="{{ route('orders.index') }}" class="search-form">
                    <div class="form-row">
                        <div class="col-md-9 mt-1">
                            <div class="form-row">
                                <div class="col-6"><input type="text" class="form-control form-control-sm" name="search" placeholder="搜索"></div>
                                <div class="col-auto"><button class="btn btn-primary btn-sm">搜索</button></div>
                                <div class="col-auto"><a href="{{ route('orders.index') }}">清除</a></div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-1">
                            <select name="sort" class="form-control form-control-sm float-right">
                                <option value="">全部类型</option>
                                <option value="order_out">出库清单</option>
                                <option value="order_in">入库清单</option>
                            </select>
                        </div>
                    </div>
                </form>
                <ul class="list-group mt-3">
                    @foreach($orders as $order)
                        <li class="list-group-item">
                            <div class="card">
                                <div class="card-header">
                                    订单号：{{ $order->no }}
                                    <span class="ml-5">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                    <span class="float-right">单据类型：@if($order->is_out)<span style="color: red;">出库清单</span>@else<span style="color: green;">入库清单</span>@endif</span>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">名称</th>
                                                <th class="text-center">型号</th>
                                                <th class="text-center">货架号</th>
                                                <th class="text-center">数量</th>
                                                <th class="text-center">状态</th>
                                                <th class="text-center">操作</th>
                                            </tr>
                                            </thead>
                                            @foreach($order->items as $index => $item)
                                                <tr data-id="{{ $order->id }}">
                                                    <td class="text-center" @if($order->closed) style="text-decoration: line-through;" @endif>
                                                        {{ $item->product->title }}
                                                    </td>
                                                    <td class="text-center" @if($order->closed) style="text-decoration: line-through;" @endif>
                                                        {{ $item->product->type }}
                                                    </td>
                                                    <td class="text-center" @if($order->closed) style="text-decoration: line-through;" @endif>
                                                        {{ $item->product->location }}
                                                    </td>
                                                    <td class="text-center" @if($order->closed) style="text-decoration: line-through;" @endif>
                                                        @if($order->is_out)
                                                            <span style="color: red;">-{{ $item->amount }}</span>
                                                        @else
                                                            <span style="color: green;">+{{ $item->amount }}</span>
                                                        @endif
                                                    </td>
                                                    @if($index === 0)
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            @if($order->closed)
                                                                <div style="color: red;">订单已关闭</div>
                                                            @elseif($order->refund_status !== \App\Models\Order::SHIP_STATUS_PENDING)
                                                                <div style="color: red;">退货状态：{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
                                                            @else
                                                                <div style="color: green;">物流状态：{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }} </div>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            <div>
                                                                <a class="btn btn-primary btn-sm" href="{{ route('orders.show', ['order' => $order->id]) }}">查看订单</a>
                                                            </div>

                                                            @if($order->closed)
                                                                <div class="mt-2">
                                                                    <button class="btn btn-success btn-sm btn-restore" type="button">恢复订单</button>
                                                                </div>
                                                            @else
                                                                <div class="mt-2">
                                                                    <button class="btn btn-danger btn-sm btn-remove" type="button">关闭订单</button>
                                                                </div>
                                                            @endif

                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <b>对方单位：</b>{{ join(' ', $order->address) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <b>操作员：</b>{{ $order->user->name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                </tr>
                                        </table>
                                    </div>
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
    var filters = {!! json_encode($filters) !!};
    $(document).ready(function () {

        $('.search-form input[name=search]').val(filters.search);
        $('.search-form select[name=sort]').val(filters.sort);
        $('.search-form select[name=sort]').on('change', function() {
            $('.search-form').submit();
        });
        // 监听 移除 按钮的点击事件
        $('.btn-remove').click(function () {
            // $(this) 可以获取到当前点击的 移除 按钮的 jQuery 对象
            // closest() 方法可以获取到匹配选择器的第一个祖先元素，在这里就是当前点击的 移除 按钮之上的 <tr> 标签
            // data('id') 方法可以获取到我们之前设置的 data-id 属性的值，也就是对应的 SKU id
            var id = $(this).closest('tr').data('id');
            swal({
                title: "确认要将该订单关闭？",
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
