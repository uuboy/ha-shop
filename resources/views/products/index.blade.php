@extends('layouts.app')
@section('title', '商品列表')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-body">
                <!-- 筛选组件开始 -->
                <form action="{{ route('products.index') }}" class="search-form">
                    <div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-6"><input type="text" class="form-control form-control-sm" name="search" placeholder="搜索"></div>
                                <div class="col-auto"><button class="btn btn-primary btn-sm">搜索</button></div>
                                <div class="col-auto"><a href="{{ route('products.index') }}">清除</a></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="order" class="form-control form-control-sm float-right">
                                <option value="">排序方式</option>
                                <option value="title_asc">名称升序排列</option>
                                <option value="title_desc">名称降序排列</option>
                                <option value="type_asc">规格升序排列</option>
                                <option value="type_desc">规格降序排列</option>
                                <option value="location_asc">货架号升序排列</option>
                                <option value="location_desc">货架号降序排列</option>
                                <option value="stock_asc">库存升序排列</option>
                                <option value="stock_desc">库存降序排列</option>
                            </select>
                        </div>
                    </div>
                </form>
                <!-- 筛选组件结束 -->
                <div class="row products-list mt-3">

                        <div class="table-responsive-md col-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <th class="text-center">
                                        名称
                                    </th>
                                    <th class="text-center">
                                        规格
                                    </th>
                                    <th class="text-center">
                                        货架号
                                    </th>
                                    <th class="text-center">
                                        库存
                                    </th>
                                    <th class="text-center">
                                        操作
                                    </th>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)

                                        <tr class="text-center" data-id="{{ $product->id }}">
                                            <td>
                                                <a href="{{ route('products.show', ['product' => $product->id]) }}" style="color: #333;">{{ $product->title }}</a>
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', ['product' => $product->id]) }}" style="color: #333;">{{ $product->type }}</a>
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', ['product' => $product->id]) }}" style="color: #333;">{{ $product->location }}</a>
                                            </td>
                                            <td>
                                                @if($product->stock>0)
                                                    <a href="{{ route('products.show', ['product' => $product->id]) }}" style="color: green;"><b>{{ $product->stock}}</b></a>
                                                @else
                                                    <a href="{{ route('products.show', ['product' => $product->id]) }}" style="color: red;"><b>{{ $product->stock}}</b></a>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-add" type="button">加入清单</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                </div>
                <div class="float-right">{{ $products->appends($filters)->render() }}</div>
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
        $('.search-form select[name=order]').val(filters.order);
        $('.search-form select[name=order]').on('change', function() {
            $('.search-form').submit();
        });

        $('.btn-add').click(function () {
            // 请求加入购物车接口
            var id = $(this).closest('tr').data('id');
            axios.post('{{ route('cart.add') }}', {
                product_id: id,
                amount: 1,
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
