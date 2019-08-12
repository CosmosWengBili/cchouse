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

Route::group(['middleware' => 'internal.protect'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return view('index');
        });
        Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
        // should be in auth
        Route::group(['middleware' => ['with.nested']], function () {
            Route::resource('buildings', 'BuildingController');
            Route::resource('rooms', 'RoomController');
            Route::resource('keys', 'KeyController');
            Route::resource('keyRequests', 'KeyRequestController');
            Route::resource('landlords', 'LandlordController');
            Route::resource('contactInfos', 'ContactInfoController');
            Route::resource('landlordAgents', 'LandlordAgentController');
            Route::resource('landlordContracts', 'LandlordContractController');
            Route::resource('landlordPayments', 'LandlordPaymentController');
            Route::resource('users', 'UserController');
            Route::resource('tenants', 'TenantController');
            Route::get('tenantContracts/{tenantContract}/extend', 'TenantContractController@extend')->name('tenantContracts.extend');
            Route::resource('tenantContracts', 'TenantContractController');
            Route::resource('audits', 'AuditController', ['only' => ['index', 'show']]);
            Route::resource('buildings', 'BuildingController');
            Route::resource('appliances', 'ApplianceController');
            Route::resource('maintenances', 'MaintenanceController');
            Route::resource('deposits', 'DepositController');
            Route::resource('debtCollections', 'DebtCollectionController');
            Route::resource('payLogs', 'PayLogController');
            Route::resource('tenantPayments', 'TenantPaymentController');
            Route::resource('tenantElectricityPayments', 'TenantElectricityPaymentController');
          
            // notifications
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');
            Route::post('notifications/{id}', 'NotificationController@read')->name('notifications.read');

            // excels
            Route::get('upload/{model}', 'ExcelController@upload');
            Route::post('import/{model}', 'ExcelController@import');
            Route::get('export/{model}', 'ExcelController@export');
            Route::get('example/{model}', 'ExcelController@example');
          
            // resources API
            Route::post('maintenances/markDone', 'MaintenanceController@markDone');

        });
    });

    Auth::routes();
});



