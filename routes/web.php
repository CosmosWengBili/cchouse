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
        Route::group(['middleware' => ['with.nested', 'redirect.nested']], function () {
            Route::resource('buildings', 'BuildingController');
            Route::get(
                'buildings/{building}/electricityPaymentReport/{year}/{month}',
                'BuildingController@electricityPaymentReport'
            )->name('buildings.electricityPaymentReport');
            Route::resource('rooms', 'RoomController');
            Route::resource('keys', 'KeyController');
            Route::resource('keyRequests', 'KeyRequestController');
            Route::resource('landlords', 'LandlordController');
            Route::resource('contactInfos', 'ContactInfoController');
            Route::resource('landlordAgents', 'LandlordAgentController');
            Route::resource('landlordContracts', 'LandlordContractController');
            Route::resource('landlordPayments', 'LandlordPaymentController');
            Route::resource('landlordOtherSubjects', 'LandlordOtherSubjectController');
            Route::resource('users', 'UserController');
            Route::resource('tenants', 'TenantController');
            Route::resource('tenantContracts', 'TenantContractController');
            Route::resource('companyIncomes', 'CompanyIncomeController');
            Route::get('tenantContracts/{tenantContract}/electricityDegree', 'TenantContractController@electricityDegree');
            Route::post(
                'tenantContracts/sendElectricityPaymentReportSMS',
                'TenantContractController@sendElectricityPaymentReportSMS'
            )->name('tenantContracts.sendElectricityPaymentReportSMS');
            Route::resource('audits', 'AuditController', ['only' => ['index', 'show']]);
            Route::resource('appliances', 'ApplianceController');
            Route::resource('maintenances', 'MaintenanceController');
            Route::resource('deposits', 'DepositController');
            Route::resource('debtCollections', 'DebtCollectionController');

            Route::group(['middleware' => 'payment.lock'], function () {
                Route::resource('payLogs', 'PayLogController');
                Route::resource('tenantPayments', 'TenantPaymentController');
                Route::resource('tenantElectricityPayments', 'TenantElectricityPaymentController');
            });

            Route::resource('shareholders', 'ShareHolderController');
            Route::resource('deposits', 'DepositController');

            // receipts
            Route::get('receipts', 'ReceiptController@index')->name('receipts.index');;
            Route::get('receipts/edit_invoice', 'ReceiptController@edit_invoice')->name('receipts.edit_invoice');
            Route::post('receipts/update_invoice', 'ReceiptController@update_invoice')->name('receipts.update_invoice');;

            // notifications
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');
            Route::post('notifications/{id}', 'NotificationController@read')->name('notifications.read');

            // excels
            Route::get('upload/{model}', 'ExcelController@upload');
            Route::post('import/{model}', 'ExcelController@import');
            Route::get('export/{model}', 'ExcelController@export');
            Route::get('example/{model}', 'ExcelController@example');
            Route::get('export/function/{function}', 'ExcelController@export_by_function');

            // pay off
            Route::get('payOffs', 'PayOffController@index')->name('payOffs.index');
            Route::get('payOffs/{tenant_contract}', 'PayOffController@show')->name('payOffs.show');
            Route::post('payOffs/{tenant_contract}/storePayOffPayments', 'PayOffController@storePayOffPayments')->name('payOffs.storePayOffPayments');

            // monthly report
            Route::get('monthlyReports', 'MonthlyReportController@index')->name('monthlyReports.index');
            Route::get('monthlyReports/{building}', 'MonthlyReportController@show')->name('monthlyReports.show');
            Route::post('monthlyReports/{building}/storeOtherSubjects', 'MonthlyReportController@storeOtherSubjects')->name('monthlyReports.storeOtherSubjects');
            Route::get('monthlyReports/{building}/print', 'MonthlyReportController@print')->name('monthlyReports.print');;

            // resources API
            Route::post('maintenances/markDone', 'MaintenanceController@markDone');
            Route::post('maintenances/showRecord', 'MaintenanceController@showRecord');
            Route::get('tenantContracts/{tenantContract}/extend', 'TenantContractController@extend')->name('tenantContracts.extend');
            Route::get('systemVariables', 'SystemVariableController@index')->name('system_variables.index');
            Route::get('systemVariables/{group}', 'SystemVariableController@edit')->name('system_variables.edit');
            Route::put('systemVariables/{group}', 'SystemVariableController@update')->name('system_variables.update');
            
        });
    });

    Auth::routes();

    Route::get(
        'tenantContracts/{tenantContract}/electricityPaymentReport/{year}/{month}',
        'TenantContractController@electricityPaymentReport'
    )->name('tenantContracts.electricityPaymentReport');
});



