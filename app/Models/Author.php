<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens; // Add this line
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticateContract;
use App\Models\Product;
class Author extends Model implements AuthenticateContract
{
    use HasFactory,HasApiTokens, Authenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'whatsapp_no',
        'facebook_url',
        'img_url'
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    public $timestamps=false;
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function image()
    {
        return $this->hasOne(U_image::class);
    }
}
