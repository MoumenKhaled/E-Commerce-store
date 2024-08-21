<?php

use App\Http\Controllers\api\AuthorController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\LikeController;
use App\Http\Controllers\api\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//PRODUCTS ROUTES
Route::prefix('product/')->group(function (){
    Route::group(['middleware'=>['auth:api']],function (){
        Route::post('create',[ProductController::class,'create']);
        Route::get('myList',[ProductController::class,'myList']);
        Route::put('update/{id}',[ProductController::class,'update']);
        Route::delete('delete/{id}',[ProductController::class,'delete']);
        Route::post('upload-image/{id}',[ProductController::class,'uploadImage']);
    });
    Route::get('single/{id}',[ProductController::class,'single']);
    Route::get('list',[ProductController::class,'list']);
    Route::post('search/{col}/{var}',[ProductController::class,'search']);
    Route::get('sort-UTD/{var}',[ProductController::class,'orderUTD']);
    Route::get('sort-DTU/{var}',[ProductController::class,'orderDTU']);

});

//USERS ROUTES
Route::prefix('user/')->group(function (){
    Route::group(['middleware'=>['auth:api']],function (){
        Route::get('profile',[AuthorController::class,'showProfile']);
        Route::post('logout',[AuthorController::class,'logout']);
        Route::put('edit',[AuthorController::class,'edit']);
    });
    
    Route::post('register',[AuthorController::class,'register']);
    Route::post('login',[AuthorController::class,'login']);
});
//CATEGORY ROUTES
Route::prefix('category/')->group(function(){
    Route::get('categories',[CategoryController::class,'getCategories']);
    Route::get('products/{id}',[CategoryController::class,'getProducts']);
});

//COMMENT ROUTES
Route::prefix('comment/')->group(function(){
    Route::group(['middleware'=>['auth:api']],function(){
        Route::post('comment/{product_id}',[CommentController::class,'create']);
        Route::put('update/{comment_id}',[CommentController::class,'update']);
        Route::delete('delete/{comment_id}',[CommentController::class,'delete']);
    });
    Route::get('show/{product_id}',[CommentController::class,'show']);
});
//LIKE ROUTES
Route::prefix('like/')->group(function(){
    Route::group(    ['middleware'=>['auth:api']],function (){
        Route::post('like/{comment_id}',[LikeController::class,'like']);
        Route::post('dislike/{comment_id}',[LikeController::class,'disLike']);
    });

});


Route::get('test',[ProductController::class,'test']);
