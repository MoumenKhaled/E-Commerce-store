<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Author;
use App\Models\Like;
use App\Models\Comment;
use App\Models\P_image;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //POST
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'price'=>'required',
            'expired_date'=>'required',
            'price_1_d'=>'required',
            'price_2_d'=>'required',
            'disC1'=>'required',
            'disC2'=>'required',
            'disC3'=>'required',
            'category_id'=>'required',
            'quantity'=>'required'
        ]);
        if($validator->fails())
        {
            return $validator->errors();
        }
        //CREATE DATA
        $product=new Product();
        $product->price_1_d=$request->price_1_d;
        $product->price_2_d=$request->price_2_d;
        $product->disC1=$request->disC1;
        $product->disC2=$request->disC2;
        $product->disC3=$request->disC3;
        $product->sum=Str::length($request->price);
        $product->user_id=auth()->user()->id;
        $product->name=$request->name;
        $product->price=$request->price;
        $product->main_price=$request->price;
        $product->expired_date=$request->expired_date;
        $product->description=$request->description;
        $product->category_id=$request->category_id;
        $product->quantity=$request->quantity;

        //SET IMAGE
        if($request->hasFile('image'))
        {
            $allowedFileExtension=['jpg','jpeg','png','bmp'];
            $file=$request->file('image');
            $errors=[];
            $extension=$file->getClientOriginalExtension();
            $check=in_array($extension,$allowedFileExtension);
            if($check)
            {
                $user_id=auth()->user()->id;
                $path=$file->store('public/images/products');
                $time=now();
                $name=$time . '.' . $extension;
                $img=new P_image();
                $product->img_url=$path;
                $img->user_id=$user_id;
                $img->title=$name;
                $img->path=$path;
                $img->product_id=$product->id;
                $img->save();
            }
            else
            {
                return response()->json([
                    'status'=>false,
                    'message'=>'invalid file format'
                ],500);
            }
        }
        //SAVE AND SEND RESPONSE
        $product->save();
        $product->price=$this->getPrice($product->id);
        $product1=Product::find($product->id);
        return response()->json([
            'status'=>true,
            'message'=>'product is added successfully',
            'data'=>$product1
        ],200);

    }

    public function getPrice($id)
    {
        $product=Product::find($id);
        $now=Carbon::today();
        $exp_date=$product->expired_date;
        $diff=$now->diffInDays($exp_date);
        $main_price=$product->main_price;
        $price_1_d=$product->price_1_d;
        $price_2_d=$product->price_2_d;
        $disC1=$product->disC1;
        $disC2=$product->disC2;
        $disC3=$product->disC3;
        if($diff>=$price_1_d)
        {
            $product->update(['price'=>$main_price*$disC1]);
            return ;
        }
        if($diff<$price_1_d && $diff>=$price_2_d)
        {
            $product->update(['price'=>$main_price*$disC2]);
            return ;
        }
        if($now->greaterThan($exp_date))
        {
            $product->delete();
            return true;
        }
        $product->update(['price'=>$main_price*$disC3]);


    }
    //GET
    public function list()
    {
        $products=Product::all();
        foreach($products as $product)
        {
            $this->getPrice($product->id);
        }
        $products=Product::all();
        return response()->json([
            'status'=>true,
            'message'=>'All products',
            'data'=>$products
        ],200);
    }
    //POST
    public function myList(Request $request)
    {
        $author_id=auth()->user()->id;
        $products=Product::query()->where('user_id','=',$author_id)->get();
        return response()->json([
            'status'=>true,
            'message'=>'All products',
            'data'=>$products
        ],200);
    }
    //GET
    public function single($id)
    {
        if($id < 1 || $id>count(Product::all()))
        {
            return response()->json([
                'status'=>false,
                'message'=>'invalid index'
            ],500);
        }
        $product=Product::find($id);
        $this->getPrice($id);
        $category_name=Category::find($product->category_id)['name'];
        $counter=$product['counter'];
        Product::find($id)->update(['counter'=>$counter+1]);
        return response()->json([
            'status'=>true,
            'data'=>$product,
            'category'=>$category_name
        ],200);
    }
    //POST
    public function update(Request $request,$id)
    {
        //VALIDATION
        $user_id=auth()->user()->id;
        if(!Product::where(['id'=>$id])->exists())
        {
            return response()->json([
                'status'=>false,
                'message'=>'this product is not found'
            ],500);
        }
        if(Product::find($id)['user_id']!=$user_id)
        {
            return response()->json([
                'status'=>false,
                'message'=>'you can not edit this product'
            ],500);
        }
        //UPDATE DATA
        $product=Product::find($id);
        $product->name=isset($request->name) ? $request->name : $product->name;
        $product->price=isset($request->price) ? $request->price : $product->price;
        $product->category_id=isset($request->category_id) ? $request->category_id : $product->category_id;
        $product->description=isset($request->description) ? $request->description : $product->description;
        $product->quantity=isset($request->quantity) ? $request->quantity : $product->quantity;
        //SAVE DATA
        $product->save();
        //SEND RESPONSE
        return response()->json([
            'status'=>true,
            'message'=>'product is updated successfully',
            'data'=>$product
        ],200);
    }
    public function delete($id)
    {
        //VALIDATION
        $user_id=auth()->user()->id;
        if(!Product::where(['id'=>$id])->exists())
        {
            return response()->json([
                'status'=>false,
                'message'=>'this product is not found'
            ],500);
        }
        if(Product::find($id)['user_id']!=$user_id)
        {
            return response()->json([
                'status'=>false,
                'message'=>"you can't delete this product"
            ],500);
        }
        //DELETE
        $product=Product::find($id);
        $product->delete();
        //SEND RESPONSE
        return response()->json([
            'status'=>true,
            'message'=>'product is deleted',
            'data'=>$product
        ],200);
    }
    //POST
    public function uploadImage(Request $request,$id)
    {
        if(!$request->hasFile('fileName'))
        {
            return response()->json([
                'status'=>false,
                'message'=>'file not found'
            ],500);
        }
        $allowedFileExtension=['jpg','jpeg','png','bmp'];
        $file=$request->file('fileName');
        $errors=[];
        $extension=$file->getClientOriginalExtension();
        $check=in_array($extension,$allowedFileExtension);
        if($check)
        {
            $user_id=auth()->user()->id;
            if(!Product::find($id)['user_id']==$user_id)
        {
            return response()->json([
            'status'=>false,
            'message'=>"you can't edit this product"
            ],500);
        }
        $path=$file->store('public/images/products');
        $time=now();
        $name=$time . '.' . $extension;
        $img=new P_image();
        $img->product_id=$id;
        $img->user_id=$user_id;
        $img->title=$name;
        $img->path=$path;
        $img->save();
        }else
        {
            return response()->json([
                'status'=>false,
                'message'=>'invalid file format'
            ],500);
        }
        return response()->json([
            'status'=>true,
            'message'=>'image uploaded successfuly',
            'data'=>$img
        ],200);
    }
    //POST
    public function search($col,$var)
    {
        switch ($col) {
            case 'name':
                $var='%' . $var . '%';
                $products=Product::where('name','like',$var)->get();
                break;
            case 'description':
                $var='%' . $var . '%';
                $products=Product::where('description','like',$var)->get();
                break;

                case 'price':
                    $products=Product::where('price','like',$var)->get();
                    break;
            default:
            
            $var='%' . $var . '%';
            $product=Product::where('name','like',$var)
            ->orWhere('description','like',$var)
            ->orWhere('price','like',$var)
            ->orWhere('expired_date','like',$var)->get();
                break;
        }
        return response()->json([
            'status'=>true,
            'data'=>$product
        ],200);
    }
    public function orderDTU($var)
    {
        switch ($var) {
            case 'name':
                $products=Product::orderBy($var,'desc')->get();
                break;
            case 'price':
            $products=Product::orderBy('sum','asc')->orderBy('price','asc')->get();
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            'status'=>true,
            'data'=>$products
        ],200);
    }
    public function orderUTD($var)
    {
        switch ($var) {
            case 'name':
                $products=Product::orderBy($var,'asc')->get();
                break;
            case 'price':
            $products=Product::orderBy('sum','desc')->orderBy('price','desc')->get();
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            'status'=>true,
            'data'=>$products
        ],200);

    }


}
