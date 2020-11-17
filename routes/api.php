<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::group([
    'namespace' => 'API\V1',
    'middleware' => ['chk_login']
], function () {

    Route::post('login', 'ManageJwtTokenController@loginRequestToken');
    Route::post('token', 'ManageJwtTokenController@loginRequestToken');
});

Route::group([
    'namespace' => 'API\V1',
    'middleware' => ['jwt.verify']
], function () {
    //Cargo API
    Route::get('cargo', 'CargoController@getAllCargo');
    Route::post('cargo', 'CargoController@store');
    Route::delete('cargo/{id}', 'CargoController@delete');
    Route::get('cargo/{id}', 'CargoController@edit');
    Route::put('cargo/{id}', 'CargoController@update');
    Route::post('logout', 'LoginController@logout');
    Route::get('user-profile', 'LoginController@userProfile');
    Route::get('refresh-token', 'LoginController@refreshToken');
    //EMAIL SEND API
    Route::post('send-email', 'SendEmailController@sendEmail');
    //Location API
    Route::get('location', 'LocationController@getAllLocation');
    Route::post('location', 'LocationController@storeLocation');
    Route::put('location/{id}', 'LocationController@updateLocation');
    Route::delete('location/{id}', 'LocationController@destroyLocation');
    Route::get('location/{id}', 'LocationController@editLocation');
    //Vessel API
    Route::get('vessel', 'VesselController@getAllVessel');
    Route::post('vessel', 'VesselController@storeVessel');
    Route::put('vessel/{id}', 'VesselController@updateVessel');
    Route::delete('vessel/{id}', 'VesselController@destroyVessel');
    Route::get('vessel/{id}', 'VesselController@editVessel');
    Route::get('/vessel/sorted-list/{keyword?}','VesselController@autocomplete');
    //Truck Company
    Route::get('truck-company', 'TruckCompanyController@index');
    Route::post('truck-company', 'TruckCompanyController@store');
    Route::delete('truck-company/{id}', 'TruckCompanyController@delete');
    Route::get('truck-company/{id}', 'TruckCompanyController@edit');
    Route::put('truck-company/{id}', 'TruckCompanyController@update');
    //Trucks
    Route::get('truck', 'TruckController@getAllTrucks');
    Route::post('truck', 'TruckController@storeTrucks');
    Route::get('truck/{id}', 'TruckController@editTrucks');
    Route::put('truck/{id}', 'TruckController@updateTrucks');
    Route::delete('truck/{id}', 'TruckController@deleteTrucks');
    // Roles & Privileges
    Route::get('/role', 'UserRoleController@getRole')->name('role-privileges');
    Route::post('role', 'UserRoleController@storeRole');
    Route::put('role/{id}', 'UserRoleController@updateRole');
    Route::delete('role/{id}', 'UserRoleController@destroyRole');
    Route::get('role/{id}', 'UserRoleController@editRole');
    Route::post('role-privileges/save', 'RoleprivilegeController@savePrivileges');
    Route::get('role-privileges/getPrivileges', 'RoleprivilegeController@getPrivileges');
    //Organization API
    Route::get('/organization/{id}', 'OrganizationController@getOrganization');
    Route::put('organization/{id}', 'OrganizationController@updateOrganizationDetails');
   // Route::post('/update-organization-rate', 'OrganizationController@updateOrganizationRate');
    //Consignee API
    Route::get('consignee', 'ConsigneeController@index');
    Route::get('consignee/{id}', 'ConsigneeController@show');
    Route::put('consignee/{id}', 'ConsigneeController@update');
    Route::post('consignee', 'ConsigneeController@store');
    Route::delete('consignee/{id}', 'ConsigneeController@destroy');
    //Extra APIs
    Route::any('/shifts','ShiftController@index');
    Route::get('/departments','DepartmentController@index');
    Route::any('/roles','UserRoleController@index');
    Route::get('/countries','CountryController@index');
    Route::get('/states','StateController@index');
    Route::get('/cities','CityController@index');
    // Dashboard API
    Route::post('dashboard', 'DashboardController@fetchDetails');
    //Export API
    Route::post('csv-export','ExcelDownloadController@csvExport');
    Route::get('department', 'DepartmentController@getAllDepartments');
    Route::post('department', 'DepartmentController@storeDepartments');
    Route::get('department/{id}', 'DepartmentController@editDepartments');
    Route::put('department/{id}', 'DepartmentController@updateDepartments');
    Route::delete('department/{id}', 'DepartmentController@deleteDepartments');
     //For User
    Route::get('/users','UserController@index');
    Route::get('/user/{id?}','UserController@edit');
    Route::post('/user','UserController@store');
    Route::put('user/{id?}', 'UserController@update');
     // API for Planning truck
    Route::post('/planning/get-trucks','BtopPlanningController@getTrucks')->name('getTrucks');
    Route::post('plan/truck','BtopPlanningController@storeTruckForBtopPlan');
    Route::delete('plan/truck/{plan_id}/{truck_id}','BtopPlanningController@deleteTruckForBtopPlan');
    //Planning API
    Route::get('plan/detail/{origin_id?}','BtopPlanningController@getPlanningDetailsByBerthAndDate');
    Route::get('plan/{id?}', 'BtopPlanningController@edit');
    Route::delete('plan/{id?}', 'BtopPlanningController@delete');
    Route::post('plan', 'BtopPlanningController@store');
    Route::put('plan/{id?}', 'BtopPlanningController@update');
    /*List of challan APIs */
    Route::get('/challan/inbound-list/{plan_id?}/{destination_id?}', 'ChallanController@getInboundChallanList');
    Route::post('/challan/reconcile', 'ChallanController@reconcileChallan');
    Route::get('/challan/barcode/{plan_id?}/{destination_id?}/{truck_id?}', 'ChallanController@fetchBarcodeApi');
    Route::post('/trip/end', 'ChallanController@endTrip');     //End Trip API
    Route::post('/challan/scan', 'ChallanController@scanChallanApi');
    Route::post('/challan', 'ChallanController@createChallan');
    Route::post('user/image', 'ProfileController@profileImageUpload'); 
    //Profile API
    Route::post('updateName', 'ProfileController@updateName');
    Route::post('updatePassword', 'ProfileController@updatePassword');
    Route::get('/profile','ProfileController@index');

});

