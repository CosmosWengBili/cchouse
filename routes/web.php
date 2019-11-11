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
            Route::get('rooms/{room}/deposits', 'RoomController@deposits')->name('rooms.deposits');
            Route::resource('rooms', 'RoomController');
            Route::resource('roomMaintenances', 'RoomMaintenanceController');
            Route::resource('keys', 'KeyController');
            Route::resource('keyRequests', 'KeyRequestController');
            Route::get('landlords/createMulti', 'LandlordController@createMulti')->name('landlordMulti.create');
            Route::post('landlords/store', 'LandlordController@storeMulti')->name('landlordMulti.store');
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
            Route::get('tenantContracts/{tenantContract}/payment_recheck', 'TenantContractController@payment_recheck')
                ->name('tenantContracts.paymentRecheck');
            Route::post(
                'tenantContracts/sendElectricityPaymentReportSMS',
                'TenantContractController@sendElectricityPaymentReportSMS'
            )->name('tenantContracts.sendElectricityPaymentReportSMS');
            Route::resource('audits', 'AuditController', ['only' => ['index', 'show']]);
            Route::resource('appliances', 'ApplianceController');
            Route::resource('maintenances', 'MaintenanceController');
            Route::post('deposits/{deposit}/close', 'DepositController@close')->name('deposits.close');
            Route::post('deposits/{deposit}/transform', 'DepositController@transform')->name('deposits.transform');
            Route::resource('deposits', 'DepositController');
            Route::resource('debtCollections', 'DebtCollectionController');
            Route::post('debtCollections/export_report', 'DebtCollectionController@exportReport')->name('debtCollections.export_report');
            Route::resource('shareholders', 'ShareHolderController');
            Route::post('shareholders/export_report', 'ShareHolderController@exportReport')->name('shareholders.export');
            Route::put('editorialReviews/{id}/pass', 'EditorialReviewController@pass')->name('editorialReviews.pass');
            Route::put('editorialReviews/{id}/notPass', 'EditorialReviewController@notPass')->name('editorialReviews.notPass');
            Route::resource('editorialReviews', 'EditorialReviewController')->except(['create', 'update', 'delete']);
            Route::resource('reversalErrorCases', 'ReversalErrorCaseController');
            Route::put('reversalErrorCases/{id}/pass', 'ReversalErrorCaseController@pass')->name('reversalErrorCases.pass');

            // payments
            Route::get(
                'tenantElectricityPayments/downloadImportFile',
                'TenantElectricityPaymentController@downloadImportFile'
            )->name('tenantElectricityPayments.downloadImportFile');
            Route::post('tenantElectricityPayments/importFile', 'TenantElectricityPaymentController@importFile')
                 ->name('tenantElectricityPayments.importFile');
            Route::post(
                'tenantElectricityPayments/sendReportSMSToAll',
                'TenantElectricityPaymentController@sendReportSMSToAll'
            )->name('tenantElectricityPayments.sendReportSMSToAll');
            Route::group(['middleware' => 'payment.lock'], function () {
                Route::post('payLogs/{payLog}/transformToDeposit', 'PayLogController@transformToDeposit')->name('payLogs.transformToDeposit');

                Route::resource('payLogs', 'PayLogController');
                Route::post('payLogs/{payLog}/changeLoggable', 'PayLogController@changeLoggable')->name('payLogs.changeLoggable');
                Route::resource('tenantPayments', 'TenantPaymentController');
                Route::resource('tenantElectricityPayments', 'TenantElectricityPaymentController');
            });

            // receipts
            Route::get('receipts', 'ReceiptController@index')->name('receipts.index');
            ;
            Route::get('receipts/edit_invoice', 'ReceiptController@edit_invoice')->name('receipts.edit_invoice');
            Route::post('receipts/update_invoice', 'ReceiptController@update_invoice')->name('receipts.update_invoice');
            ;

            // notifications
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');
            Route::post('notifications/{id}', 'NotificationController@read')->name('notifications.read');

            // excels
            Route::get('upload/{model}', 'ExcelController@upload');
            Route::post('import/{model}', 'ExcelController@import');
            Route::get('export/{model}', 'ExcelController@export');
            Route::get('export/{model}/{id}/{relation}', 'ExcelController@exportRelation');
            Route::get('example/{model}', 'ExcelController@example');
            Route::get('export/function/{function}', 'ExcelController@export_by_function')->name('export.function');
            Route::post('import/function/{function}', 'ExcelController@import_by_function');

            // pay off
            Route::get('payOffs', 'PayOffController@index')->name('payOffs.index');
            Route::get('payOffs/{tenant_contract}', 'PayOffController@show')->name('payOffs.show');
            Route::get('payOffs/{tenant_contract}/history', 'PayOffController@history')->name('payOffs.history');
            Route::post('payOffs/{tenant_contract}/storePayOffPayments', 'PayOffController@storePayOffPayments')->name('payOffs.storePayOffPayments');

            // monthly report
            Route::get('monthlyReports', 'MonthlyReportController@index')->name('monthlyReports.index');
            Route::get('monthlyReports/{building}', 'MonthlyReportController@show')->name('monthlyReports.show');
            Route::get('monthlyReports/{building}/print', 'MonthlyReportController@print')->name('monthlyReports.print');
            Route::get('monthlyReports/{building}/print_tenant', 'MonthlyReportController@print_tenant')->name('monthlyReports.print_tenant');

            // resources API
            Route::post('maintenances/markDone', 'MaintenanceController@markDone');
            Route::get('maintenances/showRecord/{id}', 'MaintenanceController@showRecord');
            Route::post('maintenances/checkHasSameWorkType', 'MaintenanceController@checkHasSameWorkType')->name('maintenances.check');
            Route::post('maintenances/updateIsPrinted', 'MaintenanceController@updateIsPrinted')->name('maintenances.updateIsPrinted');
            Route::get('tenantContracts/{tenantContract}/extend', 'TenantContractController@extend')->name('tenantContracts.extend');
            Route::get('systemVariables', 'SystemVariableController@index')->name('system_variables.index');
            Route::get('systemVariables/{group}', 'SystemVariableController@edit')->name('system_variables.edit');
            Route::put('systemVariables/{group}', 'SystemVariableController@update')->name('system_variables.update');

            // engineers tool
            Route::get('engineers/api', 'EngineerController@api')->name('engineers.api');
            Route::post('engineers/api/reversal', 'EngineerController@reversal')->name('engineers.reversal');
        });
    });

    Auth::routes();

    Route::get('electricityPaymentReport/{data}', 'TenantContractController@electricityPaymentReport')
           ->name('tenantContracts.electricityPaymentReport');
});
