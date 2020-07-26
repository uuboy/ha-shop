<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group float-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>操作员：</td>
                <td>{{ $order->user->name }}</td>
                <td>操作时间：</td>
                <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td>对方单位</td>
                <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
            </tr>
            <tr>
                <td rowspan="{{ $order->items->count() + 1 }}">商品列表</td>
                <td>货品名称</td>
                <td>货品规格</td>
                <td>数量</td>
            </tr>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }}</td>
                    <td>{{ $item->product->type }}</td>
                    <td>{{ $item->amount }}</td>
                </tr>
            @endforeach
            <tr>

                <!-- 这里也新增了一个发货状态 -->
                <td>发货状态：</td>
                <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
                <td></td>
                <td></td>
            </tr>
            <!-- 订单发货开始 -->
            <!-- 如果订单未发货，展示发货表单 -->
            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
                <!-- 加上这个判断条件 -->
                @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_SUCCESS)
                <tr>
                    <td colspan="4">
                        <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">
                            <!-- 别忘了 csrf token 字段 -->
                            {{ csrf_field() }}
                            <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                                <label for="express_company" class="control-label">物流公司</label>
                                <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="输入物流公司">
                                @if($errors->has('express_company'))
                                    @foreach($errors->get('express_company') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                                <label for="express_no" class="control-label">物流单号</label>
                                <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="输入物流单号">
                                @if($errors->has('express_no'))
                                    @foreach($errors->get('express_no') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success" id="ship-btn">发货</button>
                        </form>
                    </td>
                </tr>
                @endif
            @else
                <!-- 否则展示物流公司和物流单号 -->
                <tr>
                    <td>物流公司：</td>
                    <td>{{ $order->ship_data['express_company'] }}</td>
                    <td>物流单号：</td>
                    <td>{{ $order->ship_data['express_no'] }}</td>
                </tr>
            @endif
            <!-- 订单发货结束 -->
            @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
                <tr>
                    <td>退货状态：</td>
                    <td colspan="2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</td>
                    <td>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {

</script>
