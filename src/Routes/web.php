<?php

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

//root
Route::any('/', function () {
    if(Auth::check()){
        return view('home');
    }
    else{
        return redirect()->route('login');
    }
})->name('root');


Route::group(['namespace' => '\ITHilbert\LaravelKit\Http\Controllers',
              'middleware' => ['web', 'auth']], function () {

    Route::any('vue', 'VueController@vue')->name('vue');
    Route::post('vue-submit', 'VueController@vuesubmit')->name('vue-submit');

});

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');
