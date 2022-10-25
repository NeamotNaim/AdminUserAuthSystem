<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;  
use App\Http\Controllers\Admin\HomeController;
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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

//admin route
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
              //login route
         Route::namespace('Auth')->middleware('guest:admin')->group(function(){
              Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');      
              Route::post('login',[AuthenticatedSessionController::class, 'store'])->name('adminlogin');    
           });
        Route::middleware('admin')->group(function(){
          Route::get('dashboard',[HomeController::class,'index'])->name('dashboard');       
          
        });
        Route::post('admin/logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout');  
          
     });
       
     
           // Admin  by video provider 
// Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
//     Route::namespace('Auth')->middleware('guest:admin')->group(function(){
//         // login route
//         Route::get('login','AuthenticatedSessionController@create')->name('login');
//         Route::post('login','AuthenticatedSessionController@store')->name('adminlogin');
//     });
//     Route::middleware('admin')->group(function(){
//         Route::get('dashboard','HomeController@index')->name('dashboard');

//         Route::get('admin-test','HomeController@adminTest')->name('admintest');
//         Route::get('editor-test','HomeController@editorTest')->name('editortest');

//         Route::resource('posts','PostController');

//     });
//     Route::post('logout','Auth\AuthenticatedSessionController@destroy')->name('logout');
// });