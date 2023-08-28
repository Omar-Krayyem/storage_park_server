<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category(){
        return $this->belongsTo(productCategory::class,'product_category_id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function stocks(){
        return $this->hasMany(Stock::class, 'product_id');
    }
}
