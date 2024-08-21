<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Product;
class LikeController extends Controller
{
    public function like($product_id)
    {
        $user_id=auth()->user()->id;
        if(Like::where([
            ['user_id','=',$user_id],
            ['product_id','=',$product_id]
        ])->exists())
        {
            $like=Like::query()->where(['user_id'=>$user_id,'product_id'=>$product_id]);
            $like->delete();
            $product=Product::find($product_id);
            $likes=$product['likes'];
            Product::find($product_id)->update(['likes'=>$likes-1]);
            return response()->json([
                'status'=>true,
                'message'=>'disliked'
            ],200);
        }
        $product=Product::find($product_id);
        $likes=$product['likes'];
        Product::find($product_id)->update(['likes'=>$likes+1]);
        $like=new Like();
        $like->user_id=$user_id;
        $like->product_id=$product_id;
        $like->save();
    }
    public  function  disLike($id)
    {
        $user_id=auth()->user()->id;
        if(Like::query()->where(['user_id'=>$user_id,'product_id'=>$id])->exists())
        {
        return response()->json([
            'status'=>false,
            'message'=>"you didn't like it"
        ],500);
        }
        else{

        }
    }
}
