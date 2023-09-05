<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function users(){
        return $this->hasMany(User::class, 'user_type_id');
    }
}
