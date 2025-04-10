<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AttendanceController;

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
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
Route::get('/user',[AuthController::class,function(Request $request){
    return response()->json([
        'user'=>$request->user()
    ]);
}])->middleware('auth:sanctum');

Route::get('/attendances',[AttendanceController::class,'index'])->middleware('auth:sanctum');
Route::post('/attendances',[AttendanceController::class,'store'])->middleware('auth:sanctum');
Route::get('/export-attendance', [AttendanceController::class, 'exportMonthlyReport'])->middleware('auth:sanctum');
Route::post('/attendance/export-pdf', [AttendanceController::class, 'exportAttendance'])->middleware('auth:sanctum');
Route::post('apply', [AttendanceController::class, 'applyLeave'])->middleware('auth:sanctum');
Route::get('leaves',[AttendanceController::class,'myLeaves'])->middleware('auth:sanctum');
