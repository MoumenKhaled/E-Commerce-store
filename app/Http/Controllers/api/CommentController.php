<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;
class CommentController extends Controller
{
    public function create($id,Request $request)
    {
        $user_id=auth()->user()->id;
        $user_name=auth()->user()->name;
        $product_id=Product::find($id)['id'];
        $description=$request->description;
        $comment=new Comment();
        $comment->user_id=$user_id;
        $comment->product_id=$product_id;
        $comment->description=$description;
        $comment->user_name=$user_name;
        $comment->save();
        return response()->json([
            'status'=>true,
            'message'=>'comment is added successfully',
            'comment'=>$comment
        ],200);
    }

    public function show($id)
    {

        $comments=new Comment();
        $comments=Comment::where('product_id',$id)->get();
        return response()->json([
            'status'=>true,
            'data'=>$comments
        ],200);
    }

    public  function update(Request $request,$comment_id)
    {
        $user_id=auth()->user()->id;
        $comment=Comment::find($comment_id);
                if($comment['user_id']!=$user_id)
        {
            return response()->json([
                'status'=>false,
                'message'=>"you can't edit this comment"
            ],500);
        }
        else
        {
            $description=$request->description;
            $comment->update(['description'=>$description]);
            return response()->json([
                'status'=>true,
                'message'=>'comment is updatedsuccessfully',
                'comment'=>$comment
            ],200);
        }
    }

    public function delete($id)
    {
        $user_id=auth()->user()->id;
        $comment=Comment::find($id);
        if($comment['user_id']!=$user_id)
        {
            return response()->json([
                'status'=>false,
                'message'=>"you can't delete this comment"
            ],500);
        }
        else{
            $comment->delete();
            return response()->json([
                'status'=>true,
                'message'=>"comment is deleted"
            ],200);
        }
    }
}
