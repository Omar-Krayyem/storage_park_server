<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;
    
    public function orders(){
        return $this->hasMany(Order::class, 'order_type_id');
    }
}
