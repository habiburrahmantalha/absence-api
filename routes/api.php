<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\AbsenceController;

Route::middleware('auth.token')->group(function () {
    Route::get('/absences', [AbsenceController::class, 'index']);
    Route::get('/absences/total', [AbsenceController::class, 'total']);
});

//Route::get('/absences', [AbsenceController::class, 'index']);
//Route::get('/absences/total', [AbsenceController::class, 'total']);
