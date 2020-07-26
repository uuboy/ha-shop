<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/products')->name('root');


Auth::routes();

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => 'auth'], function() {
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
    Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');

    Route::get('products', 'ProductsController@index')->name('products.index');
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
    Route::get('products/{product}', 'ProductsController@show')->name('products.show');
    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

    Route::get('cart', 'CartController@index')->name('cart.index');
    Route::post('cart', 'CartController@add')->name('cart.add');
    Route::delete('cart/{product}', 'CartController@remove')->name('cart.remove');
    Route::get('orders', 'OrdersController@index')->name('orders.index');
    Route::post('orders', 'OrdersController@store')->name('orders.store');
    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
    Route::post('orders/{order}/refund', 'OrdersController@refund')->name('orders.refund');
    Route::post('orders/{order}/refund_success', 'OrdersController@refund_success')->name('orders.refund.success');
    Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
    Route::post('orders/{order}/close', 'OrdersController@close')->name('orders.close');
    Route::post('orders/{order}/restore', 'OrdersController@restore')->name('orders.restore');
    Route::post('orders/{order}/ship', 'OrdersController@ship')->name('orders.ship');

});



