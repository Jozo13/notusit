<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);
Route::get('/logout', [ApiAuthController::class, 'logout']);
Route::get('/user_profile', [ApiAuthController::class, 'userProfile']);

Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/user', [UserController::class, 'getUser']);
Route::post('/update_user', [UserController::class, 'updateUser']);
Route::post('/delete_user', [UserController::class, 'deleteUser']);

Route::post('/image', [ImageController::class, 'createImage']);
Route::get('/image', [ImageController::class, 'getImage']);
Route::get('/images', [ImageController::class, 'getImages']);
Route::post('/delete_image', [ImageController::class, 'deleteImage']);

Route::post('/product', [ProductController::class, 'createProduct']);
Route::post('/update_product', [ProductController::class, 'updateProduct']);
Route::get('/product', [ProductController::class, 'getProduct']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::post('/delete_product', [ProductController::class, 'deleteProduct']);

Route::post('/category', [CategoryController::class, 'createCategory']);
Route::post('/update_category', [CategoryController::class, 'updateCategory']);
Route::get('/category', [CategoryController::class, 'getCategory']);
Route::get('/categories', [CategoryController::class, 'getCategories']);
Route::post('/delete_category', [CategoryController::class, 'deleteCategory']);

Route::post('/comment', [CommentController::class, 'createComment']);
Route::post('/update_comment', [CommentController::class, 'updateComment']);
Route::get('/comment', [CommentController::class, 'getComment']);
Route::get('/comments', [CommentController::class, 'getComments']);
Route::post('/delete_comment', [CommentController::class, 'deleteComment']);
