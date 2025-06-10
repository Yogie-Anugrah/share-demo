<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShareController;

Route::get('/share/{token}', [ShareController::class, 'handleShare']);
Route::get('/share-page', [ShareController::class, 'showSharePage']);
Route::get('/proxy-api/{platform}', [ShareController::class, 'proxyAPI']);
