@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-body product-info">
                <div class="row">
                    <div class="col-12">
                        <div class="title text-center">货品名称：{{ $product->title }}</div>
                        <div class="sales_and_reviews">
                            <div class="sold_count">规格 <span class="count">{{ $product->type }}</span></div>
                            <div class="sold_count">库存 <span class="count">{{ $product->stock }}</span></div>
                            <div class="review_count">货架号 <span class="count">{{ $product->location }}</span></div>
                        </div>
                        <div class="row align-items-center">
                            <div class="cart_amount col-4"><label>数量</label><input type="text" class="form-control form-control-sm" value="1"><span>件</span></div>
                            <div class="col-8 text-right">
                                @if($favored)
                                    <button class="btn btn-danger btn-disfavor">取消收藏</button>
                                @else
                                    <button class="btn btn-success btn-favor">❤ 收藏</button>
                                @endif
                                <button class="btn btn-primary btn-add-to-cart ml-4">加入清单</button>
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
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});


        // 监听收藏按钮的点击事件
        $('.btn-favor').click(function () {
            axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
                .then(function () {
                    swal('操作成功', '', 'success')
                        .then(function () {  // 这里加了一个 then() 方法
                            location.reload();
                        });
                }, function(error) {
                    if (error.response && error.response.status === 401) {
                        swal('请先登录', '', 'error');
                    }  else if (error.response && error.response.data.msg) {
                        swal(error.response.data.msg, '', 'error');
                    }  else {
                        swal('系统错误', '', 'error');
                    }
                });
        });

        $('.btn-disfavor').click(function () {
            axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
                .then(function () {
                    swal('操作成功', '', 'success')
                        .then(function () {
                            location.reload();
                        });
                });
        });

        // 加入购物车按钮点击事件
        $('.btn-add-to-cart').click(function () {
            // 请求加入购物车接口
            axios.post('{{ route('cart.add') }}', {
                product_id: {{ $product->id }},
                amount: $('.cart_amount input').val(),
            })
                .then(function () { // 请求成功执行此回调
                    swal('加入购物车成功', '', 'success')
                        .then(function() {
                            location.href = '{{ route('cart.index') }}';
                        });
                }, function (error) { // 请求失败执行此回调
                    if (error.response.status === 401) {
                        // http 状态码为 401 代表用户未登陆
                        swal('请先登录', '', 'error');
                    } else if (error.response.status === 422) {
                        // http 状态码为 422 代表用户输入校验失败
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else {
                        // 其他情况应该是系统挂了
                        swal('系统错误', '', 'error');
                    }
                })
        });

    });
</script>
@endsection
