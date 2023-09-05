<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function products(){
        return $this->hasMany(Product::class,'product_category_id');
    }
}
