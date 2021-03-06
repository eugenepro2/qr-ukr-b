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

Auth::routes();

Route::resources([
  'package' => 'PackageController',
  'project' => 'ProjectController',
  'items' => 'ItemController'
]);
Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('storage/{file}', function ($file) {
  $path = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $file);
  return response()->file($path);
});