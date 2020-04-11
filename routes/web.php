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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/survey/{id}', ['as'=>'home.survey', 'uses'=>'SurveysController@survey']);
Route::patch('/survey/{id}', ['as'=>'home.survey', 'uses'=>'SurveysController@update']);
Route::get('/compare/{id}', ['as'=>'home.compare', 'uses'=>'SurveysController@compare']);

Route::group(['middleware' => 'campleader'], function(){

    Route::get('/admin','AdminController@index');

    Route::resource('admin/users', 'AdminUsersController');
    Route::get('usersList', 'AdminUsersController@usersList');
    Route::post('admin/users/uploadFile', 'AdminUsersController@uploadFile');

    Route::resource('admin/answers', 'AdminAnswersController');

    Route::resource('admin/camps', 'AdminCampsController');

    Route::resource('admin/questions', 'AdminQuestionsController');
    Route::post('admin/questions/uploadFile', 'AdminQuestionsController@uploadFile');

    Route::resource('admin/surveys', 'AdminSurveysController');

    Route::resource('admin/chapters', 'AdminChaptersController');

    Route::get('admin/run-migrations', function () {
    	return Artisan::call('migrate', ["--force" => true ]);
	});
});
