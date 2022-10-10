<?php
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\UserPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiKey;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  //return $request->user();
//});
Route::post('login' ,[AuthController::class, 'login']);
Route::post('register' ,[AuthController::class, 'register']);
Route::post('logout' ,[AuthController::class, 'logout'])->middleware(['auth:sanctum']);
//Route::get('user-profile', [AuthController::class, 'userProfile']);
//Route::get('user',[AuthApiController::class ,'getAuthenticatedUser'])->middleware('auth:sanctum')->name('auth.user');
	Route::post('/password/email', [AuthController::class ,'forgetPassword'])->name('password.email');
	Route::post('/password/reset', [AuthController::class ,'resetPassword'])->name('password.reset');
Route::apiResource('comments',CommentController::class);
Route::apiResource('users',UserController::class);
Route::apiResource('posts',PostController::class);
Route::apiResource('friends',FriendController::class);
Route::get('user/post',[UserPostController::class,'index']);

