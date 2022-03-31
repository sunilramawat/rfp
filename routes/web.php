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
/*
Route::get('/', function () {
    return view('index');
});
*/
Route::get('terms/','ApiController@terms');
Route::get('privacypolicy/','ApiController@privacypolicy');
Route::get('activation/{id}/{code}','ApiController@activation');
Route::get('admin-activation/{id}/{code}','admin\LoginController@resetPass');
Route::post('do-reset-password','admin\LoginController@doresetpass');