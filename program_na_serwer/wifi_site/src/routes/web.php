<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Auth::routes();

Route::get('/', 'HomeController@dashboard')->name('home');
Route::get('/home', 'HomeController@dashboard');
Route::get('/user/profile', 'UserController@profile')->name('show-user-profile');
Route::post('/user/profile/details', 'UserController@updateDetails')->name('updateDetails');
Route::post('/user/profile/password', 'UserController@passwordUpdate')->name('passwordUpdate');

Route::get('/team/list', 'TeamController@index')->middleware('isTeamOwner');
Route::get('/team/invite', 'TeamController@invite')->middleware('isTeamOwner');
Route::get('/team/switch/{id}', array('as'=> 'change-user-team', 'uses' => 'TeamController@switch'));
Route::post('/team/invite/send', 'TeamController@inviteSend')->middleware('isTeamOwner');
Route::get('/team/locations', 'TeamController@locations')->middleware('isTeamOwner');
Route::post('/team/locations/add', 'TeamController@addNewLocation')->middleware('isTeamOwner');
Route::post('/team/locations/edit', 'TeamController@editLocation')->middleware('isTeamOwner');
Route::get('/team/locations/remove/{id}', 'TeamController@deleteLocation')->middleware('isTeamOwner');
Route::get('/team/locations_roles', 'TeamController@locationsRolesPermissions');
Route::post('/team/locations_roles', 'TeamController@addLocationRolesPermissions');
Route::get('/team/user/remove/{id}', 'TeamController@removeUserFromTeam')->middleware('isTeamOwner');


Route::get('/devices', 'DevicesController@index');
Route::get('/devices/location/{locationId}', 'DevicesController@devicesByLocation')->middleware('CheckUserLocation');


Route::get('/devices/details/{deviceId}', array('as' => 'devicesShowDetails', 'uses' => 'DevicesController@details'))->middleware('canSeeDevice');
Route::get('/devices/details/settings/history/{deviceId}/{id}', array('as' => 'settingsHistory', 'uses' => 'DevicesController@settingsHistory'))->middleware('canSeeDevice');
Route::post('/devices/details/update/{response}', array('as' => 'updateDeviceDetails', 'uses' => 'DevicesController@update'))->middleware('canSeeDevice');
Route::post('/devices/details/update-options', array('as' => 'updateDeviceOptions', 'uses' => 'DevicesController@updateOptions'));
Route::post('/devices/problems/{id}/{deviceId}/read/{response}', 'DevicesController@errorsMarkAsRead')->middleware('canSeeDevice');


Route::get('/reports/location/{locationId}/counters', 'ReportsController@counters')->middleware('CheckUserLocation');
