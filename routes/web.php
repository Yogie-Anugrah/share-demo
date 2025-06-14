<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShareController;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;

Route::get('/share/{token}', [ShareController::class, 'handleShare']);
Route::get('/share-page', [ShareController::class, 'showSharePage']);
Route::get('/proxy-api/{platform}', [ShareController::class, 'proxyAPI']);
// Route::get('/note/{platform}', [ShareController::class, 'renderPlatformNote']);
// Route::get('/share-page', [ShareController::class, 'buildSharePage']);

Route::get('/note-view', function (Request $request) {
    $title = urldecode($request->query('title', 'Untitled'));
    $rawContent = urldecode($request->query('content', ''));

    $converter = new CommonMarkConverter();
    $htmlContent = $converter->convertToHtml($rawContent);

    return view('red-note-single', [
        'title' => $title,
        'content' => $htmlContent
    ]);
});


