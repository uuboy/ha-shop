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
                            </select>
                        </div>
                    </div>
                </form>
                <!-- 筛选组件结束 -->
                <div class="row products-list">
                    @foreach($products as $product)
                        <div class="col-3 product-item">
                            <div class="product-content">
                                <div class="top">
                                    <div class="img">
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                        </a>
                                    </div>
                                    <div class="price"><b>规格：</b>{{ $product->type }}</div>
                                    <div class="title">
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
                                    </div>
                                </div>
                                <div class="bottom">
                                    <div class="sold_count">库存 <span>{{ $product->stock }}件</span></div>
                                    <div class="review_count">货架号 <span>{{ $product->location }}</span></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
    })


</script>
@endsection
