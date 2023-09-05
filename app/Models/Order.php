<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function order_type(){
        return $this->belongsTo(OrderType::class,'order_type_id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
