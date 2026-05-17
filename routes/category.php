<?php

use Illuminate\Support\Facades\Route;
use Hexatex\LaravelCategory\Http\Controllers\Admin;
use Hexatex\LaravelCategory\Http\Controllers\Web;

Route::bindCategoryModel();

// Admin Routes
Route::middleware(['auth', 'can:admin-features.access'])->group(function () {

    // Api
    Route::prefix('admin/api')->middleware('api')->group(function () {
        Route::post('category/index', [Admin\Api\CategoryController::class, 'index'])->middleware('can:category.index')->name('admin.category.index');
        Route::get('category/{category}', [Admin\Api\CategoryController::class, 'get'])->middleware('can:view,category')->name('admin.category.get');
        Route::post('category', [Admin\Api\CategoryController::class, 'store'])->middleware('can:category.store')->name('admin.category.store');
        Route::put('category/{category}', [Admin\Api\CategoryController::class, 'update'])->middleware('can:update,category')->name('admin.category.update');
        Route::delete('category/{category}', [Admin\Api\CategoryController::class, 'destroy'])->middleware('can:destroy,category')->name('admin.category.destroy');
    });

    // Web
    Route::prefix('admin')->middleware('web')->group(function () {
        Route::get('category', [Admin\CategoryController::class, 'index'])->middleware('can:category.index')->name('admin.web.category.index');
        Route::get('category/{category}', [Admin\CategoryController::class, 'show'])->middleware('can:view,category')->name('admin.web.category.show');
    });
});

// Public Api Routes
Route::prefix('api')->middleware('api')->group(function () {
    Route::post('category/index', [Web\Api\CategoryController::class, 'index'])->name('web.category.index');
    Route::get('category/{category}', [Web\Api\CategoryController::class, 'get'])->middleware('can:view,category')->name('web.category.get');
});

// Public Web Routes
Route::middleware('web')->group(function () {
    Route::get('category', [Web\CategoryController::class, 'index'])->name('category.index');
    Route::get('category/{category}', [Web\CategoryController::class, 'show'])->middleware('can:view,category')->name('category.show');
});
