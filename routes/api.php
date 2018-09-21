<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('Call', 'ConnectPostgreSQL@Call');
Route::get('Form', 'ConnectPostgreSQL@Form');
Route::get('Crontab_call', 'ConnectPostgreSQL@Crontab_call');
Route::get('Crontab_form', 'ConnectPostgreSQL@Crontab_form');
Route::get('SearchHeroBase', 'ConnectPostgreSQL@SearchHeroBase');

Route::get('accountCall', 'ConnectPostgreSQL@accountCall');
Route::get('accountForm', 'ConnectPostgreSQL@accountForm');
Route::get('campaignCall', 'ConnectPostgreSQL@campaignCall');
Route::get('campaignForm', 'ConnectPostgreSQL@campaignForm');
Route::get('test', 'ConnectPostgreSQL@test');

Route::get('leadServiceAccount', 'LeadService@leadServiceAccount');
Route::get('leadServiceCampaign', 'LeadService@leadServiceCampaign');
Route::get('leadServiceChannel', 'LeadService@leadServiceChannel');
Route::get('leadServiceCall', 'LeadService@leadServiceCall');
Route::get('leadServiceForm', 'LeadService@leadServiceForm');