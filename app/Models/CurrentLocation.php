<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentLocation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function worker(){
        return $this->belongsTo(User::class,'worker_id');
    }
}
