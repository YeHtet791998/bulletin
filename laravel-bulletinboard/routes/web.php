<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\User\UserController;

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

Route::get('/', function () {

    return redirect()->route('postlist');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ----------------------------auth ------------------------------//

Route::controller(RegisterController::class)->group(function () {

    Route::get('/user/signup', 'showSignup')->name('signup');
    Route::post('/user/signup', 'submitSignup')->name('signup');
    
    Route::middleware(['admin'])->group(function () {
    Route::get('/user/register', 'showRegister')->name('register');
    Route::post('/user/register', 'submitRegister')->name('register');
    Route::get('/user/register/confirm', 'showRegisterConfirm')->name('registerconfirm');
    Route::post('/user/register/confirm', 'submitRegisterConfirm')->name('registerconfirm');
   
});
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

// ----------------------------users------------------------------//
Route::controller(UserController::class)->group(function () {
    Route::middleware(['auth'])->group(function () {
    Route::get('/user/profile', 'showProfile')->name('userprofile');
    Route::get('/user/profile/edit', 'showProfileEdit')->name('userprofileedit');
    Route::post('/user/profile/edit', 'submitProfileEdit')->name('userprofileedit');
    Route::get('/user/profile/edit/confirm', 'showProfileEditConfirm')->name('profileeditconfirm');
    Route::post('/user/profile/edit/confirm', 'submitProfileEditConfirm')->name('profileeditconfirm');
    Route::get('user/change-password','showChangePassword')->name('changepassword');
    Route::post('user/change-password','submitChangePassword')->name('changepassword');
});

    Route::middleware(['admin'])->group(function () {
    Route::get('/user/list', 'index')->name('userlist');
    Route::delete('/user/delete', 'deleteUser')->name('userdelete');
    Route::get('user/search','searchUser')->name('usersearch');
});
});


// ---------------------------- posts ------------------------------//
Route::controller(PostController::class)->group(function () {
    Route::get('/post/list', 'index')->name('postlist');
    Route::get('post/search','searchPost')->name('postsearch');
    Route::get('post/download/','downloadPostCSV')->name('postdownload');

    Route::middleware(['auth'])->group(function () {
    Route::get('/post/create', 'showPostCreate')->name('postcreate');
    Route::post('/post/create', 'submitPostCreate')->name('postcreate');
    Route::get('/post/create/confirm', 'showPostConfirm')->name('postconfirm');
    Route::post('/post/create/confirm', 'submitPostConfirm')->name('postconfirm');
    Route::get('/post/edit/{id}', 'showPostEdit')->name('postedit');
    Route::post('/post/edit/{id}', 'submitPostEdit')->name('postedit');
    Route::get('/post/edit/{id}/confirm', 'showPostEditConfirm')->name('posteditconfirm');
    Route::post('/post/edit/{id}/confirm', 'submitPostEditConfirm')->name('posteditconfirm');
    Route::delete('/post/delete', 'deletePost')->name('postdelete');
    Route::get('post/upload/','showPostUpload')->name('postupload');
    Route::post('post/upload/','submitPostUpload')->name('postupload');
});
});


