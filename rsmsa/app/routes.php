<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//process login form
Route::post('login', array('uses'=>'LoginController@login'));

Route::get('/', function(){

	return View::make('index')->with('apps',AppEntity::all());
});

/*
 * 
 * These are app routes. Routes for getting app specific information
 * 
 */
Route::get('/apps/manifests', 'AppController@getManifests');
Route::get('/app/{id}', 'AppController@getApp');
Route::get('/app/{id}/manifest', 'AppController@getManifest');
Route::get('/app/{id}/{file}','AppController@getFile' );
Route::get('/app/{id}/views/{file}', 'AppController@getView');
Route::get('/app/{id}/controllers/{file}', 'AppController@getController');
/*
 * These are routes to api requests
 */
Route::get('/api/request/{tag}', 'AndroidController@processtag');


//Vehicle Controller Rooutes
Route::get('/api/vehicle/{plate_number}', "VehicleController@getVehicle");
Route::get('/api/vehicle/{plate_number}/offences', "VehicleController@getOffences");
Route::get('/api/vehicle/{plate_number}/offences/paid', "VehicleController@getPaidOffences");
Route::get('/api/vehicle/{plate_number}/offences/notpaid', "VehicleController@getNotPaidOffences");

//Police Controller Rooutes
Route::get('/api/police/{rank_no}', "PoliceController@getPolice");


//Station Controller Rooutes
Route::get('/api/station/{id}', "StationController@getStation");
//Driver Controller Rooutes
Route::get('/api/driver/{license_number}', "DriverController@getDriver");
Route::get('/api/driver/{license_number}/offences', "DriverController@getOffences");
Route::get('/api/driver/{license_number}/offences/paid', "DriverController@getPaidOffences");
Route::get('/api/driver/{license_number}/offences/notpaid', "DriverController@getNotPaidOffences");

//Offence Controller Rooutes
Route::get('/api/offence/registry', "OffenceController@getOffenceRegistry");
Route::get('/api/offence/report', "OffenceController@getReport");
Route::post('/api/offence/stats', "OffenceController@getStats");
Route::get('/api/offence/registry/{id}/offences', "OffenceController@getOffenceRegistryOffences");
Route::get('/api/offence/{id}', "OffenceController@getOffence");

Route::get('/api/offences', "OffenceController@getOffences");
Route::post('/api/offence/', "OffenceController@processOffencePost");
Route::get('/api/offence/{id}/events/', "OffenceController@getEvents");
Route::get('/api/offence/{id}/delete/', "OffenceController@delete");

