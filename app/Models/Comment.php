<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;


    public $timestamps=false;

    protected $fillable=[
        'product_id',
        'user_id',
        'user_name',
        'description'
    ];
    protected $hidden=[
        'product_id',
        'user_id',
        'created_at',
        'updated_at',
        'id'
    ];
    public function product()
    {
        return $this->belongsTo(Porduct::class);
    }
}
