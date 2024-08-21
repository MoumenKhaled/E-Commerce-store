<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Author;
use App\Models\U_image;
use Illuminate\Support\Facades\Validator;
class AuthorController extends Controller
{
    //POST
    public function register(Request $request)
    {
         //VALIDATION
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:authors,email',
            'phone_no'=>'required',
            'password'=>'required|confirmed'
        ]);
        if($validator->fails())
        {
            return $validator->errors();
        }

        //CREATE DATA
        $user= new Author();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=bcrypt($request->password);
        $user->phone_no=$request->phone_no;
        $user->whatsapp_no=$request->whatsapp_no;
        $user->facebook_url=$request->facebook_url;
        $user->img_url=$request->img_url;
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
            $user_id=$user->id;
            $path=$file->store('public/images/users');
            $time=now();
            $name=$time . '.' . $extension;
            $img=new U_image();
            $user->img_url=$path;
            $img->user_id=$user_id;
            $img->title=$name;
            $img->path=$path;
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
        $user->save();
        return response()->json([
            'status'=>true,
            'message'=>'account registered successfully',
            'data'=>$user
        ],200);
    }

    //POST
    public function login(Request $request)
    {
        //VALIDATION
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        //VALIDATE DATA
        if(!auth()->attempt($loginData))
        {
            return response()->json([
                'status'=>false,
                'message'=>'invalid data'
            ],500);
        }
        //MAKE TOKEN
        $token=auth()->user()->createToken('authToken')->accessToken;
        //SEND RESPONSE

        return response()->json([
            'status'=>true,
            'message'=>'logged in successfully',
            'access-token'=>$token
        ],200);

    }
    //GET
    public function showProfile()
    {
        $user_data=auth()->user();
        return response()->json([
            'status'=>true,
            'data'=>$user_data
        ],200);
    }
    //POST
    public function edit(Request $request)
    {
        $id=auth()->user()->id;
        $user=Author::find($id);
        $user->name=isset($request->name)? $request->name : $user->name;
        $user->email=isset($request->email)? $request->email : $user->email;
        $user->phone_no=isset($request->phone_no)? $request->phone_no : $user->phone_no;
        $user->whatsapp_no=isset($request->whatsapp_no)? $request->whatsapp_no : $user->whatsapp_no;
        $user->facebook_url=isset($request->facebook_url)? $request->facebook_url : $user->facebook_url;
        $user->save();
        return response()->json([
            'status'=>true,
            'message'=>'profile updated successfule',
            'data'=>$user
        ],200);
    }
    //GET
    public function logout(Request $request)
    {
        //GET TOKEN VALUE
        $token=$request->user()->token();
        //REVOKE TOKEN
        $token->revoke();
        //SEND RESPONSE
        return response()->json([
            'status'=>true,
            'message'=>'user logged out successfully'
        ],200);
    }
}
