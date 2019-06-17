<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/',function(){ return view('db/show'); });

//Route::get('/{time?}',"DashboardController@show");

Route::get('/',function(){
	$time_now=date("Y-m-d H:i:s");
	$prev_logs=(file_exists("json/access.logs.json"))?file_get_contents("json/access.logs.json"):"";
	$logs=$prev_logs.",{'accessed_at':'$time_now'}";
	$msge="accessed at:".date("Y-m-d H:i:s");
	file_put_contents("json/access.logs.json",$logs);
	return view('dash'); 
});

Route::get('/reports',function(){
	return view('reports'); 
});
Route::get("/live","DashboardController@live");
Route::get("/other_data/","DashboardController@other_data");
Route::get("/results_printing_stats/","DashboardController@getResultsPrintingStatistics");

