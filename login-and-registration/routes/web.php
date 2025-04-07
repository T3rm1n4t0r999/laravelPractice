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
    Route::get('/', 'HomeController@index')->name('home.index');

    Route::group(['middleware' => ['guest']], function() {
        /**
         * Register Routes
         */
        Route::get('/register', 'RegisterController@show')->name('register.show');
        Route::post('/register', 'RegisterController@register')->name('register.perform');

        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login.show');
        Route::post('/login', 'LoginController@login')->name('login.perform');

    });

    Route::group(['middleware' => ['auth']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');
        // profile
        Route::get('/profile', 'ProfileController@index')->name('profile.profile');
        Route::get('/import', 'ImportController@index')->name('profile.import');
        //upload
        Route::post('/profile/upload', 'FileController@upload')->name('files.upload');
        //generate file link
        Route::post('/files/generateFileLink/', 'FileController@generateFileLink')->name('files.generateFileLink');

        Route::get('/files', 'FileController@getUserFiles')->name('files.getUserFiles');

        Route::get('/files/{token}', 'FileController@showPasswordForm')->name('files.showPasswordForm');
        Route::post('/files/{token}', 'FileController@downloadFile')->name('files.download');
    });
});
