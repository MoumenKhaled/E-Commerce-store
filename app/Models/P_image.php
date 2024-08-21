<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_image extends Model
{
    protected $fillable=['title','path','user_id','product_id'];
    protected $hidden=['user_id','product_id','path'];
    public $timestamps=false;
    use HasFactory;
}
