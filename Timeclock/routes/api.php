<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TimeRecordController;
use App\Models\User;


Route::post('/login-teste', function () {
    $user = User::firstOrCreate(
        ['email' => 'funcionario@empresa.com'],
        ['name' => 'Antonio Felipe', 'password' => bcrypt('123456')]
    );
    $token = $user->createToken('token-teste')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pontos', TimeRecordController::class);
});
