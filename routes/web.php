<?php

use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
        Route::get('login', 'index')->name('login');
        Route::post('login', 'login')->name('auth.login');
    });

});

Route::middleware('auth')->group(function () {

    // Logout
    Route::get('auth/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');

    Route::controller(\App\Http\Controllers\TaskController::class)->group(function () {
        Route::get('tasks', 'index')->name('tasks.index');
        Route::get('getData/tasks', 'getData')->name('tasks.getData');
        Route::get('getData/tasks-active', 'getDataActive')->name('tasks.active.getData');

        Route::post('tasks/{task}/toggleStatus', 'toggleStatus')->name('tasks.toggleStatus');
        Route::post('tasks', 'store')->name('tasks.store');
        Route::delete('tasks/{task}', 'destroy')->name('tasks.destroy');
        Route::put('tasks/{task}', 'update')->name('tasks.update');
        Route::get('tasks/{task}', 'show')->name('tasks.show');
    });

    Route::controller(\App\Http\Controllers\JournalController::class)->group(function () {
        Route::get('journals', 'index')->name('journals.index');
        Route::get('getData/journals', 'getData')->name('journals.getData');

        Route::post('journals', 'store')->name('journals.store');
        Route::delete('journals/{journal}', 'destroy')->name('journals.destroy');
        Route::put('journals/{journal}', 'update')->name('journals.update');
        Route::get('journals/{journal}', 'show')->name('journals.show');
    });

    Route::controller(\App\Http\Controllers\RandomPickerController::class)->group(function () {
        Route::get('random-pickers', 'index')->name('random-pickers.index');
        Route::get('random-pickers/getData', 'getData')->name('random-pickers.getData');

        Route::post('random-pickers', 'store')->name('random-pickers.store');
        Route::delete('random-pickers/{randomPicker}', 'destroy')->name('random-pickers.delete');
    });


    Route::controller(\App\Http\Controllers\DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('home');
        Route::get('/dashboard/weekly-productivity', 'weeklyProductivity')->name('dashboard.weeklyProductivity');
    });
});

Route::get('/artisan', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return view('welcome');
});
