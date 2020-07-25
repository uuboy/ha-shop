@extends('layouts.app')
@section('title', '查看订单')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                @if($order->is_out)
                    <h4>出库单详情</h4>
                @else
                    <h4>入库单详情</h4>
                @endif

            </div>
            <div class="card-body">
                <div class="table-responsive-md">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>货品名称</th>
                            <th>货品规格</th>
                            <th>货架号</th>
                            <th>数量</th>
                        </tr>
                        </thead>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td>
                                     {{ $item->product->title }}
                                </td>
                                <td>
                                     {{ $item->product->type }}
                                </td>
                                <td>
                                     {{ $item->product->location }}
                                </td>
                                @if($order->is_out)
                                    <td style="color: red;">
                                      -{{ $item->amount }}
                                    </td>
                                @else
                                    <td style="color: green;">
                                      +{{ $item->amount }}
                                </td>
                                @endif

                            </tr>
                        @endforeach
                        <tr><td colspan="4"></td></tr>
                    </table>
                </div>
                <div class="order-bottom">
                    <div class="order-info pr-3">
                        <div class="row"><div class="col-md-3 col-8 text-right">收货地址：</div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ join(' ', $order->address) }}</div></div>
                        <div class="row"><div class="col-md-3 col-8 text-right">订单备注：</div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ $order->remark ?: '-' }}</div></div>
                        <div class="row"><div class="col-md-3 col-8 text-right">订单编号：</div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ $order->no }}</div></div>
                        <div class="order-express mt-4">
                            <div class="row"><div class="col-md-3 col-8 text-right"><b>物流状态：</b></div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div></div>
                            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED || $order->ship_status === \App\Models\Order::SHIP_STATUS_RECEIVED)
                                <div class="row mt-2"><div class="col-md-3 col-8 text-right"><b>物流公司：</b></div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ $order->ship_data['express_company'] }}</div></div>
                                <div class="row mt-2"><div class="col-md-3 col-8 text-right"><b>物流单号：</b></div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ $order->ship_data['express_no'] }}</div></div>
                            @endif

                            @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PROCESSING || $order->refund_status === \App\Models\Order::REFUND_STATUS_SUCCESS || $order->refund_status === \App\Models\Order::REFUND_STATUS_FAILED)
                                <div class="row mt-2"><div class="col-md-3 col-8 text-right"><b>退货编号：</b></div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ $order->refund_no }}</div></div>
                                <div class="row mt-2"><div class="col-md-3 col-8 text-right"><b>退货状态：</b></div><div class="col-md-9 col-10 offset-md-0 offset-2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div></div>
                            @endif
                       </div>
                    </div>

                    <div class="order-summary">


                    @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
                        <div class="container mt-4">
                            <form role="form" id="express_form">
                                <div class="form-group row justify-content-center">
                                    <label class="col-form-label col-md-3 col-sm-4 text-md-right">物流公司:</label>
                                    <div class="col-md-7 col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="express_company" id="express_company" placeholder="输入物流公司">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-form-label col-md-3 col-sm-4 text-md-right">物流单号:</label>
                                    <div class="col-md-7 col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="express_no" id="express_no" placeholder="输入物流单号">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <div class="offset-md-3 col-md-7">
                                        <button class="btn btn-primary btn-sm" id="ship-btn" type="button">发货</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @elseif($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                       <div class="container">
                            <div class="row justify-content-center align-items-center mt-5">
                                <div class="col-md-4 text-center mt-3">
                                    <button class="btn btn-danger" type="button" id="refund-btn">退货</button>
                                </div>
                                <div class="col-md-4 text-center mt-3">
                                    <button class="btn btn-success" type="button" id="received-btn">确认收货</button>
                                </div>
                            </div>
                       </div>
                    @elseif($order->ship_status === \App\Models\Order::SHIP_STATUS_RECEIVED)
                        <div class="container">
                            <div class="row justify-content-center align-items-center mt-5">
                                <div class="col-12 text-center">
                                    <h3 style="color: green;">已完成</h3>
                                </div>
                            </div>
                       </div>
                    @elseif($order->refund_status === \App\Models\Order::REFUND_STATUS_SUCCESS)
                        <div class="container">
                            <div class="row justify-content-center align-items-center mt-5">
                                <div class="col-12 text-center">
                                    <h3 style="color: green;">退货已完成</h3>
                                </div>
                            </div>
                       </div>
                    @else
                        <div class="contariner">
                            <div class="row justify-content-center align-items-center mt-5">
                                <div class="col-md-4 text-center mt-3">
                                    <button class="btn btn-danger" type="button" id="refund-failed">退货失败</button>
                                </div>
                                <div class="col-md-4 text-center mt-3">
                                    <button class="btn btn-success" type="button" id="refund-successed">退货成功</button>
                                </div>
                            </div>
                        </div>
                    @endif
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
        $('#ship-btn').click(function () {
            // 构建请求参数，将用户选择的地址的 id 和备注内容写入请求参数
            var req = {
                express_company: $('#express_form').find("input[name=express_company]").val(),
                express_no: $('#express_form').find('input[name=express_no]').val(),
            };
            axios.post('{{ route('orders.ship', $order->id) }}', req)
                .then(function (response) {
                    swal('发货成功', '', 'success')
                        .then(() => {
                            location.reload();
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        // http 状态码为 422 代表用户输入校验失败
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else if (error.response.status === 403) { // 这里判断状态 403
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        // 其他情况应该是系统挂了
                        swal('系统错误', '', 'error');
                    }
            });
        });

        var req2 ={

        };

        $('#refund-btn').click(function () {
            axios.post('{{ route('orders.refund', $order->id) }}')
                .then(function (response) {
                    swal('退货成功', '', 'success')
                        .then(() => {
                            location.reload();
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        // http 状态码为 422 代表用户输入校验失败
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else if (error.response.status === 403) { // 这里判断状态 403
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        // 其他情况应该是系统挂了
                        swal('系统错误', '', 'error');
                    }
            });
        });

        $('#refund-successed').click(function () {
            axios.post('{{ route('orders.refund.success', $order->id) }}')
                .then(function (response) {
                    swal('退货成功', '', 'success')
                        .then(() => {
                            location.reload();
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        // http 状态码为 422 代表用户输入校验失败
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else if (error.response.status === 403) { // 这里判断状态 403
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        // 其他情况应该是系统挂了
                        swal('系统错误', '', 'error');
                    }
            });
        });

        $('#received-btn').click(function () {
            axios.post('{{ route('orders.received', $order->id) }}')
                .then(function (response) {
                    swal('确认收货成功', '', 'success')
                        .then(() => {
                            location.reload();
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        // http 状态码为 422 代表用户输入校验失败
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else if (error.response.status === 403) { // 这里判断状态 403
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        // 其他情况应该是系统挂了
                        swal('系统错误', '', 'error');
                    }
            });
        });
    });
</script>
@endsection


