<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps=false;

    protected $fillable=[
        'name',
        'price',
        'main_price',
        'expired_date',
        'user_id',
        'description',
        'quantity',
        'counter',
        'likes',
        'img_url',
        'sum',
        'price_1_d',
        'price_2_d',
        'disC1',
        'disC2',
        'disC3'
    ];
    protected $hidden=['category_id','main_price','created_at','updated_at','sum','price_1_d','price_2_d','price_3_d',
        'disC1','disC2','disC3'
        ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->hasOne(Author::class);
    }
    public function image()
    {
        return $this->hasOne(P_image::class);
    }
    public function comments()
    {
        return $this->hasMany('Comment','product_id');
    }
}
