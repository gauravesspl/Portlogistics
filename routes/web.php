<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('/login');
});
Auth::routes();

Route::group([
    'namespace' => 'API\V1',
    'middleware' => ['web', 'auth']
], function () {
    //Cargo API
    Route::get('cargo', 'CargoController@getAllCargo');
    Route::post('cargo', 'CargoController@store');
    Route::delete('cargo/{id}', 'CargoController@delete');
    Route::get('cargo/{id}', 'CargoController@edit');
    Route::put('cargo/{id}', 'CargoController@update');
    // updated by Gaurav Agrawal on 28-10-2020 to modify api for consignee in PLP1-173
    // Consignee API
    Route::get('consignee', 'ConsigneeController@index');
    Route::get('consignee/{id}', 'ConsigneeController@show');
    Route::put('consignee/{id}', 'ConsigneeController@update');
    Route::post('consignee', 'ConsigneeController@store');
    Route::delete('consignee/{id}', 'ConsigneeController@destroy');

    //Organization API
    Route::get('/organization', 'OrganizationController@getOrganization');
    Route::put('organization/{id}', 'OrganizationController@updateOrganizationDetails');
   // Route::post('/update-organization-rate', 'OrganizationController@updateOrganizationRate');
    //Department API
    Route::get('department', 'DepartmentController@getAllDepartments');
    Route::post('department', 'DepartmentController@storeDepartments');
    Route::get('department/{id}', 'DepartmentController@editDepartments');
    Route::put('department/{id}', 'DepartmentController@updateDepartments');
    Route::delete('department/{id}', 'DepartmentController@deleteDepartments');
    // Roles & Privileges
    Route::get('/role', 'UserRoleController@getRole');
    Route::post('role', 'UserRoleController@storeRole');
    Route::put('role/{id}', 'UserRoleController@updateRole');
    Route::delete('role/{id}', 'UserRoleController@destroyRole');
    Route::get('role/{id}', 'UserRoleController@editRole');
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
    //Vessel API
    Route::get('vessel', 'VesselController@getAllVessel');
    Route::post('vessel', 'VesselController@storeVessel');
    Route::put('vessel/{id}', 'VesselController@updateVessel');
    Route::delete('vessel/{id}', 'VesselController@destroyVessel');
    Route::get('vessel/{id}', 'VesselController@editVessel');
    Route::get('/vessel/sorted-list/{keyword?}','VesselController@autocomplete');
    //Location API
    Route::get('/location', 'LocationController@getAllLocation');
    Route::post('/location', 'LocationController@storeLocation');
    Route::put('/location/{id}', 'LocationController@updateLocation');
    Route::delete('/location/{id}', 'LocationController@destroyLocation');
    Route::get('/location/{id}', 'LocationController@editLocation');
     //For User
    Route::get('/users','UserController@index');
    Route::get('/user','UserController@add');
    Route::get('/user/{id?}','UserController@edit');
    Route::post('/user','UserController@store');
    Route::put('user/{id?}', 'UserController@update'); 
    // Dashboard API
    Route::post('dashboard', 'DashboardController@fetchDetails');
    Route::post('csv-export','ExcelDownloadController@csvExport');
    //Profile API
    Route::get('/profile','ProfileController@index')->name('profile');
    Route::post('updateName', 'ProfileController@updateName');
    Route::post('updatePassword', 'ProfileController@updatePassword');
    Route::post('validateCurrentPassword', 'ProfileController@validateCurrentPassword');
    Route::post('user/image', 'ProfileController@profileImageUpload'); 
    // Dashboard API
    Route::get('/home', 'DashboardController@show')->name('home');
    //Planning 

    Route::get('plans', 'PlanController@list');
    Route::post('plan/list-ajax', 'PlanController@getPlanningList');
    Route::get('plan', 'PlanController@add');
    Route::get('plan/{id?}', 'PlanController@edit');
    Route::delete('plan/{id?}', 'PlanController@delete');
    Route::post('plan', 'PlanController@store');
    Route::put('plan/{id?}', 'PlanController@update');
    

    // Add Truck For Planning
    Route::post('/planning/truck/add','PlanController@create');
    Route::post('/planning/get-trucks','PlanController@getTrucks')->name('getTrucks');
    Route::post('plan/truck','PlanController@storeTruckForBtopPlan');
    Route::delete('plan/truck/{plan_id}/{truck_id}','PlanController@deleteTruckForBtopPlan');

    //Challan API
    Route::get('/challans', 'ChallanController@index');
    Route::post('/challan/list-ajax', 'ChallanController@getChallanList');
    Route::post('/challan/reconcile', 'ChallanController@reconcileChallan'); 

    Route::post('role-privileges/save', 'RoleprivilegeController@savePrivileges');
    Route::any('role-privileges/getPrivileges', 'RoleprivilegeController@getPrivileges');
});
Route::group([
    'namespace' => 'API\V1',
    'prefix' => 'admin',
    'middleware' => ['web', 'auth']
], function () {
    //cache routes
    Route::post('clear-cache-data', 'BaseController@clearCache');
    Route::post('clear-cache-menu', 'BaseController@clearCacheMenu');
    Route::get('cache/clear', 'BaseController@clearCacheView'); 
});
Route::any('/password/sendemail', 'API\V1\SetPasswordController@sendEmail');
Route::any('/password/save', 'API\V1\SetPasswordController@savePassword');