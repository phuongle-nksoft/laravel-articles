<?php
use Illuminate\Support\Facades\Route;
use Nksoft\Articles\Controllers\ArticleCategoriesController;
use Nksoft\Articles\Controllers\ArticlesController;
use Nksoft\Articles\Controllers\BannersController;
use Nksoft\Articles\Controllers\BlocksController;
use Nksoft\Articles\Controllers\MenusController;
use Nksoft\Articles\Controllers\PagesController;

Route::group(['prefix' => 'api/admin', 'middleware' => 'web'], function () {
    Route::resources([
        'article-categories' => ArticleCategoriesController::class,
        'articles' => ArticlesController::class,
        'pages' => PagesController::class,
        'blocks' => BlocksController::class,
        'menus' => MenusController::class,
        'banners' => BannersController::class,
        'promotion-images' => BlocksController::class,
    ]);
});
