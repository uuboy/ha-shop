<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['amount'];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
