<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['namespace' => 'App\Http\Controllers'], function()
{
    /**
     * Home Routes
     */
    Route::get('/', 'PageController@showHomePage')->name('home.index');
    Route::get('/error', 'PageController@showErrorPage')->name('failure.error');

    Route::group(['middleware' => ['guest']], function() {
        /**
         * Register Routes
         */
        Route::get('/register', 'AuthController@showRegisterPage')->name('register.show');
        Route::post('/register', 'AuthController@register')->name('register.perform');

        /**
         * Login Routes
         */
        Route::get('/login', 'AuthController@showLoginPage')->name('login.show');
        Route::post('/login', 'AuthController@login')->name('login.perform');

    });

    Route::group(['middleware' => ['auth']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'AuthController@logout')->name('logout.perform');
        // profile
        Route::get('/profile', 'PageController@showProfilePage')->name('profile.profile');
        Route::get('/import', 'PageController@showImportPage')->name('profile.import');
        //upload
        Route::post('/import/upload', 'FileController@upload')->name('import.upload');
        //generate file link
        Route::post('/files/generateFileLink/', 'FileController@generateFileLink')->name('files.generateFileLink');

        Route::get('/profile/files', 'FileController@getUserFiles')->name('files.getUserFiles');

        Route::get('/files/{token}', 'FileController@showPasswordForm')->name('files.showPasswordForm');
        Route::post('/files/{token}', 'FileController@downloadFile')->name('files.download');
    });
});
