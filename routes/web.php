<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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


Route::get('/survey/{survey}', ['as'=>'survey.survey', 'uses'=>'SurveysController@survey']);
Route::patch('/survey/{survey}', ['as'=>'survey.update', 'uses'=>'SurveysController@update']);
Route::get('/compare/{user}', ['as'=>'survey.compare', 'uses'=>'SurveysController@compare']);
Route::patch('/compare/{id}', ['as'=>'survey.finish', 'uses'=>'SurveysController@finish']);
Route::get('/compare/{survey}/downloadPDF', ['as'=>'survey.downloadPDF','uses'=>'SurveysController@downloadPDF']);

Route::resource('/post', 'PostController');

Route::get('/user/{user}', ['as'=>'home.user', 'uses'=>'UsersController@index']);
Route::get('/profile/{user}', ['as'=>'home.profile', 'uses'=>'UsersController@edit']);
Route::patch('/changeClassifications/{id}/{color}', ['as'=>'users.changeClassifications', 'uses'=>'UsersController@changeClassifications']);
Route::patch('/user/{id}', ['as'=>'home.update', 'uses'=>'UsersController@update']);


Route::group(['middleware' => 'campleader'], function(){

    Route::get('/admin','AdminController@index');

    Route::get('/admin/changes','AdminController@changes');

    Route::resource('admin/users', 'AdminUsersController');
    Route::get('users/createDataTables', ['as'=>'users.CreateDataTables','uses'=>'AdminUsersController@createDataTables']);
    Route::post('admin/users/uploadFile', 'AdminUsersController@uploadFile');
    Route::get('admin/users/download',  ['as'=>'users.download', 'uses'=>'AdminUsersController@download']);
    Route::post('admin/users/import',  ['as'=>'users.import', 'uses'=>'AdminUsersController@import']);

    Route::resource('admin/answers', 'AdminAnswersController');

    Route::post('admin/camps/opensurvey', ['as'=>'camps.opensurvey','uses'=>'AdminCampsController@opensurvey']);
    Route::resource('admin/camps', 'AdminCampsController');

    Route::resource('admin/questions', 'AdminQuestionsController');
    Route::post('admin/questions/uploadFile', 'AdminQuestionsController@uploadFile');

    Route::resource('admin/surveys', 'AdminSurveysController');
    Route::get('surveys/createDataTables', ['as'=>'surveys.CreateDataTables','uses'=>'AdminSurveysController@createDataTables']);

    Route::resource('admin/chapters', 'AdminChaptersController');
    Route::resource('admin/classifications', 'AdminClassificationController');


});

Route::get('admin/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true ]);
});

Route::get('admin/run-deployment', function () {
    echo 'config:cache <br>';
    Artisan::call('config:cache');
    echo 'view:cache <br>';
    Artisan::call('view:cache');
    return true;
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
