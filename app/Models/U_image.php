<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class U_image extends Model
{
    protected $fillable=['title','path','user_id'];
    protected $hiiden=['user_id','path'];
    public $timestamps=false;
    use HasFactory;
}
