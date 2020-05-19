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
            'banners' => WebController::class,
        ]);
    });
});
Route::group(['namespace' => 'Nksoft\Articles\Controllers'], function () {
    Route::get('pages/{id}', 'PagesController@show')->name('pages');
    Route::get('article-categories/{id}', 'ArticleCategoriesController@show')->name('article-categories');
    Route::get('articles/{id}', 'ArticlesController@show')->name('articles');
    Route::get('blocks/{id}', 'BlocksController@show')->name('blocks');
});
