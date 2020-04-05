<?php

use Illuminate\Support\Facades\Route;
use Nksoft\Master\Controllers\WebController;

Route::group(['middleware' => 'web'], function () {
    Route::group(['middleware' => 'nksoft', 'prefix' => 'admin'], function () {
        Route::resources([
            'article-categories' => WebController::class,
            'articles' => WebController::class,
            'pages' => WebController::class,
            'blocks' => WebController::class,
            'menus' => WebController::class,
        ]);
    });
});
