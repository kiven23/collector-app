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

Route::group(['prefix' => 'auth', 'middleware' => 'api'], function ($router) {

	Route::post('login', 'AuthController@login');
	Route::post('logout', 'AuthController@logout');
	Route::post('refresh', 'AuthController@refresh');
	Route::post('me', 'AuthController@me');
	Route::post('roles', 'AuthController@roles');
	Route::post('permissions', 'AuthController@permission');
});

Route::group(['prefix' => 'users', 'middleware' => ['jwt.auth', 'user_clearance']], function () {

	Route::get('/', 'UserController@all')->name('users');
	Route::post('/', 'UserController@store_api')->name('user.store');
	Route::put('/update', 'UserController@update_api')->name('user.update');
	Route::patch('/delete/multiple', 'UserController@delete_multiple')->name('user.delete');
});

Route::group(['prefix' => 'divisions', 'middleware' => ['jwt.auth', 'division_clearance']], function () {
	Route::get('/', 'DivisionController@all')->name('divisions');
	Route::post('/', 'DivisionController@store_api')->name('division.store');
	Route::put('/update', 'DivisionController@update_api')->name('division.update');
	Route::patch('/delete/multiple', 'DivisionController@delete_multiple')->name('division.delete');
});

Route::group(['prefix' => 'departments', 'middleware' => ['jwt.auth', 'department_clearance']], function () {
	Route::get('/', 'DepartmentController@all')->name('departments');
	Route::post('/', 'DepartmentController@store_api')->name('department.store');
	Route::put('/update', 'DepartmentController@update_api')->name('department.update');
	Route::patch('/delete/multiple', 'DepartmentController@delete_multiple')->name('department.delete');
});

Route::group(['prefix' => 'positions', 'middleware' => ['jwt.auth', 'position_clearance']], function () {

	Route::get('/', 'PositionController@all')->name('positions');
	Route::post('/', 'PositionController@store_api')->name('position.store');
	Route::put('/update', 'PositionController@update_api')->name('position.update');
	Route::patch('/delete/multiple', 'PositionController@delete_multiple')->name('position.delete');
});

Route::group(['prefix' => 'user-employments', 'middleware' => ['jwt.auth', 'user_employment_clearance']], function () {

	Route::get('/', 'UserEmploymentController@all')->name('user.employments');
	Route::put('/update', 'UserEmploymentController@update_api')->name('user.employment.update');
});

Route::group(['prefix' => 'branches', 'middleware' => ['jwt.auth', 'branch_clearance']], function () {

	Route::get('/', 'BranchController@all')->name('branches');
	Route::post('/', 'BranchController@store_api')->name('branch.store');
	Route::put('/update', 'BranchController@update_api')->name('branch.update');
	Route::patch('/delete/multiple', 'BranchController@delete_multiple')->name('branch.delete');
});

Route::group(['prefix' => 'bscheds', 'middleware' => ['jwt.auth', 'branch_sched_clearance']], function () {

	Route::get('/', 'BranchScheduleController@all')->name('bscheds');
	Route::post('/', 'BranchScheduleController@store_api')->name('bsched.store');
	Route::put('/update', 'BranchScheduleController@update_api')->name('bsched.update');
	Route::patch('/delete/multiple', 'BranchScheduleController@delete_multiple')->name('bsched.delete');
});

Route::group(['prefix' => 'regions', 'middleware' => ['jwt.auth', 'region_clearance']], function () {

	Route::get('/', 'RegionController@all')->name('regions');
	Route::post('/', 'RegionController@store_api')->name('region.store');
	Route::put('/update', 'RegionController@update_api')->name('region.update');
	Route::patch('/delete/multiple', 'RegionController@delete_multiple')->name('region.delete');
});

Route::group(['prefix' => 'roles', 'middleware' => ['jwt.auth', 'role_clearance']], function () {

	Route::get('/', 'RoleController@all')->name('roles');
	Route::post('/', 'RoleController@store_api')->name('role.store');
	Route::put('/update', 'RoleController@update_api')->name('role.update');
	Route::patch('/delete/multiple', 'RoleController@delete_multiple')->name('role.delete');
});

Route::group(['prefix' => 'permissions', 'middleware' => ['jwt.auth', 'permission_clearance']], function () {

	Route::get('/', 'PermissionController@all')->name('permissions');
	Route::post('/', 'PermissionController@store_api')->name('permission.store');
	Route::put('/update', 'PermissionController@update_api')->name('permission.update');
	Route::patch('/delete/multiple', 'PermissionController@delete_multiple')->name('permission.delete');
});

// products
Route::group(['prefix' => 'product-brands', 'middleware' => ['jwt.auth', 'product_brand_clearance']], function () {

	Route::get('/', 'ProductBrandController@all')->name('product.brands');
	Route::post('/', 'ProductBrandController@store_api')->name('product.brand.store');
	Route::put('/update', 'ProductBrandController@update_api')->name('product.brand.update');
	Route::patch('/delete/multiple', 'ProductBrandController@delete_multiple')->name('product.brand.delete');
});

Route::group(['prefix' => 'product-categories', 'middleware' => ['jwt.auth', 'product_category_clearance']], function () {

	Route::get('/', 'ProductCategoryController@all')->name('product.categories');
	Route::post('/', 'ProductCategoryController@store_api')->name('product.category.store');
	Route::put('/update', 'ProductCategoryController@update_api')->name('product.category.update');
	Route::patch('/delete/multiple', 'ProductCategoryController@delete_multiple')->name('product.category.delete');
});

Route::group(['prefix' => 'product-items', 'middleware' => ['jwt.auth', 'product_item_clearance']], function () {

	Route::get('/', 'ProductItemController@all')->name('product.items');
	Route::post('/', 'ProductItemController@store_api')->name('product.item.store');
	Route::put('/update', 'ProductItemController@update_api')->name('product.item.update');
	Route::patch('/delete/multiple', 'ProductItemController@delete_multiple')->name('product.item.delete');
});

// Services
Route::group(['prefix' => 'service-types', 'middleware' => ['jwt.auth', 'service_type_clearance']], function () {

	Route::get('/', 'ServiceTypeController@all')->name('service.types');
	Route::post('/', 'ServiceTypeController@store_api')->name('service.type.store');
	Route::put('/update', 'ServiceTypeController@update_api')->name('service.type.update');
	Route::patch('/delete/multiple', 'ServiceTypeController@delete_multiple')->name('service.type.delete');
});
Route::group(['prefix' => 'service-categories', 'middleware' => ['jwt.auth', 'service_category_clearance']], function () {

	Route::get('/', 'ServiceCategoryController@all')->name('service.categories');
	Route::post('/', 'ServiceCategoryController@store_api')->name('service.category.store');
	Route::put('/update', 'ServiceCategoryController@update_api')->name('service.category.update');
	Route::patch('/delete/multiple', 'ServiceCategoryController@delete_multiple')->name('service.category.delete');
});
Route::group(['prefix' => 'service-providers', 'middleware' => ['jwt.auth', 'service_provider_clearance']], function () {

	Route::get('/', 'ServiceProviderController@all')->name('service.providers');
	Route::post('/', 'ServiceProviderController@store_api')->name('service.provider.store');
	Route::put('/update', 'ServiceProviderController@update_api')->name('service.provider.update');
	Route::patch('/delete/multiple', 'ServiceProviderController@delete_multiple')->name('service.provider.delete');
});

// Tickets
// Computerware
Route::group(['prefix' => 'computerware-tickets', 'middleware' => ['jwt.auth', 'computerware_ticket_clearance']], function () {

	Route::get('/', 'ComputerwareTicketController@all')->name('tickets.computerwares');
	Route::post('/', 'ComputerwareTicketController@store_api')->name('tickets.computerware.store');
	Route::put('/update', 'ComputerwareTicketController@update_api')->name('tickets.computerware.update');
	Route::patch('/delete/multiple', 'ComputerwareTicketController@delete_multiple')->name('tickets.computerware.delete');
});
// Connectivity
Route::group(['prefix' => 'connectivity-tickets', 'middleware' => ['jwt.auth', 'computerware_ticket_clearance']], function () {

	Route::get('/', 'ConnectivityTicketController@all')->name('tickets.computerwares');
	Route::post('/', 'ConnectivityTicketController@store_api')->name('tickets.connectivity.store');
	Route::put('/update', 'ConnectivityTicketController@update_api')->name('tickets.connectivity.update');
	Route::patch('/delete/multiple', 'ConnectivityTicketController@delete_multiple')->name('tickets.connectivity.delete');
});

// Power Interruption
Route::group(['prefix' => 'power-interruptions', 'middleware' => ['jwt.auth', 'power_interruption_clearance']], function () {

	Route::get('/', 'PowerInterruptionController@all')->name('powerinterruptions');
	Route::post('/', 'PowerInterruptionController@store_api')->name('powerinterruption.store');
	Route::put('/update', 'PowerInterruptionController@update_api')->name('powerinterruption.update');
	Route::patch('/delete/multiple', 'PowerInterruptionController@delete_multiple')->name('powerinterruption.delete');
});

// Pending Transactions
Route::group(['prefix' => 'pending-transactions', 'middleware' => ['jwt.auth', 'pending_clearance']], function () {

	Route::post('/index', 'PendingController@all')->name('pendingtransactions');
	Route::post('/', 'PendingController@store_api')->name('pendingtransaction.store');
	Route::patch('/addall', 'PendingController@addall')->name('pendingtransaction.addall');
	Route::put('/update', 'PendingController@update_api')->name('pendingtransaction.update');
	Route::patch('/delete', 'PendingController@delete_api')->name('pendingtransaction.delete');
});

//ARCHIVED
Route::group(['prefix' => 'archived', 'middleware' => ['jwt.auth', 'government_clearance']], function () {

	Route::get('/index', 'ArchivedController@all')->name('archived.land');
	Route::post('/store', 'ArchivedController@store')->name('archived.land.store');
	Route::post('/update', 'ArchivedController@update')->name('archived.land.store');
	Route::post('/download', 'ArchivedController@download')->name('archived.land.download');
	Route::post('/delete', 'ArchivedController@delete')->name('archived.land.delete');
});

//AGENCIES
Route::group(['prefix' => 'agencies', 'middleware' => ['jwt.auth', 'government_clearance']], function () {
	Route::get('/index', 'AgenciesController@all')->name('agencies.all');
	Route::post('/store', 'AgenciesController@store')->name('agencies.store');
	Route::post('/update', 'AgenciesController@update')->name('agencies.update');
	Route::post('/trash', 'AgenciesController@trash')->name('agencies.trash');
	Route::post('/download', 'AgenciesController@download')->name('agencies.download');
	Route::post('/delete', 'AgenciesController@delete')->name('agencies.delete');
});
Route::post('/agencies/date', 'AgenciesController@getDate')->name('agencies.date');
Route::get('/date', 'DateController@get_date')->name('getDate');

//BRANCH PUBLIC API
Route::group(['prefix' => 'branches'], function () {
	Route::get('/public', 'CustomerDigitizedReqController@branches')->name('digitizedcustomer.branches');
	//REUPLOADBRANCHCODE
	Route::get('/public/uploadbranchcode', 'CustomerDigitizedReqController@reuploadBranchCode')->name('digitizedcustomer.reuploadBranchCode');
});
//, 'middleware' => ['jwt.auth', 'sapapi_clearance']
//SAP API
Route::group(['prefix' => 'public', 'middleware' => ['jwt.auth', 'sapapi_clearance']], function () {
	//INSTALLMENT DUE
	Route::post('/index', 'SapApiController@index')->name('sap.index');
	Route::post('/installment', 'SapApiController@installment')->name('sap.installment');
	//CREDIT STANDING	
	Route::post('/credit/standing/generate', 'CreditStandingController@generate')->name('credit.standing.generate');
	Route::get('/credit/standing/index', 'CreditStandingController@index')->name('credit.standing.index');
	//INSTALLMENT LEDGER-> RECON
	Route::post('/installment/index', 'SapApiController@installment_ledger')->name('sap.installment.index');
	Route::post('/installment/create', 'SapApiController@installment_Bal')->name('sap.installment_Bal');
	Route::post('/installment/updatemanual', 'SapApiController@updatemanual')->name('sap.updatemanual');
	//CALCULATE BRANCH GRADE
	Route::get('/calculate/grade', 'SapApiController@compute_grade')->name('sap.compute_grade');
	//GET BRANCH SEGMENTCODE
	Route::get('/branch/segment', 'SapApiController@getBranchSegment')->name('sap.getBranchSegment');

	//SAPREPORTS
	Route::post('/reports/incoming/crb', 'SapRportsController@incoming_crb_generate')->name('sap.SapRports.crb');

	//Query Searching of Vehicles parts
	Route::get('/reports/queries/searchofvehicleparts', 'SapRportsController@searchofvehicleparts')->name('sap.SapRports.searchofvehicleparts');
	//Invoice Query Series Revised
	Route::get('/reports/queries/invoicequeryseriesrevised', 'SapRportsController@invoicequeryseriesrevised')->name('sap.SapRports.invoicequeryseriesrevised');
	//Martketing AR Invoice Query
	Route::get('/reports/queries/marketingarinvoicequery', 'SapRportsController@marketingarinvoicequery')->name('sap.SapRports.marketingarinvoicequery');
	//Summary of Customer DepositApplied
	Route::get('/reports/queries/summaryofcustomerdepositapplied', 'SapRportsController@summaryofcustomerdepositapplied')->name('sap.SapRports.summaryofcustomerdepositapplied');
	//Adjustments Sales Discount
	Route::get('/reports/queries/adjustmentsalesdiscount', 'SapRportsController@adjustmentsalesdiscount')->name('sap.SapRports.adjustmentsalesdiscount');
	//Recomputed Account
	Route::get('/reports/queries/recomputedaccount', 'SapRportsController@recomputedaccount')->name('sap.SapRports.recomputedaccount');
	//Ar Invoice Open Balance
	Route::get('/reports/queries/arinvoiceopenbalance', 'SapRportsController@arinvoiceopenbalance')->name('sap.SapRports.arinvoiceopenbalance');
	//Incoming Payment Customer Deposit
	Route::get('/reports/queries/incomingpaymentcustomerdeposit', 'SapRportsController@incomingpaymentcustomerdeposit')->name('sap.SapRports.incomingpaymentcustomerdeposit');
	//Incoming Payment open Balamce
	Route::get('/reports/queries/incomingpaymentopenbalance', 'SapRportsController@incomingpaymentopenbalance')->name('sap.SapRports.incomingpaymentopenbalance');

});
Route::get('/sql/test', 'SapApiController@index')->name('sap.installment.index2');
//DIGITIZED REQUIREMENT
Route::group(['prefix' => 'digitized', 'middleware' => ['jwt.auth', 'cdr_clearance']], function () {
	Route::get('/index', 'CustomerDigitizedReqController@index')->name('digitizedcustomer.index');
	Route::post('/upload', 'CustomerDigitizedReqController@upload')->name('digitizedcustomer.upload');
	Route::post('/update', 'CustomerDigitizedReqController@update')->name('digitizedcustomer.update');
	Route::post('/download', 'CustomerDigitizedReqController@download')->name('digitizedcustomer.download');
	Route::post('/trash', 'CustomerDigitizedReqController@trash')->name('digitizedcustomer.trash');
	Route::post('/delete', 'CustomerDigitizedReqController@delete')->name('digitizedcustomer.delete');
});

//BLACKLISTED CUSTOMER
Route::group(['prefix' => 'blacklisted'], function () {
	Route::get('/index', 'BlackListedController@index')->name('blacklisted.index');
	Route::post('/upload', 'BlackListedController@upload')->name('blacklisted.upload');
	Route::post('/search', 'BlackListedController@search')->name('credit.search.index');
});

//CUSTOMERS WITH OVERDUE 
Route::group(['prefix' => 'credit-dunning', 'middleware' => ['jwt.auth', 'sapapi_clearance']], function () {
	Route::post('/index', 'CreditDungLettersController@index')->name('dunning.index');
	Route::post('/download-letters', 'CreditDungLettersController@downloadLetters')->name('dunning.download_letters');
});

//VALIDATION PORTAL 
Route::group(['prefix' => 'validation-portal', 'middleware' => ['jwt.auth', 'validation_portal_clearance']], function () {
	Route::get('/check-auth', 'ValidationPortalController@checkAuth')->name('validation-portal.check_auth');
	Route::post('/validate-template', 'ValidationPortalController@validateTemplate')->name('validation-portal.validate_template');
	Route::post('/validate-good-receipt-to-serial', 'ValidationPortalController@validateGoodReceiptToSerial')->name('validation-portal.validate_good_receipt_to_serial');
	Route::post('/validate-bp-master-cardcode-ar-invoice', 'ValidationPortalController@validateBPMasterDataCardCodeArInvoice')->name('validation-portal.validate_bp_master_cardcode_ar_invoice');
	Route::post('/export-to-excel', 'ValidationPortalController@exportToExcel')->name('validation-portal.export_to_excel');
});

//REVOLVING FUND
Route::group(['prefix' => 'revolving-fund', 'middleware' => ['jwt.auth']], function () {
	Route::get('/index', 'RevolvingFundController@index')->name('revolving_fund.index');
	Route::post('/create', 'RevolvingFundController@create')->name('revolving_fund.create');
});

//to be removed
Route::get('/credit-dunning/download-letters/{branch}/{aging}', 'CreditDungLettersController@downloadLettersGet');
Route::get('/credit-dunning/download-letter/{branch}/{aging}', 'CreditDungLettersController@downloadLetter')->name('dunning.download_letter');
Route::get('/credit-dunning', 'CreditDungLettersController@test');
Route::get('/get-branches', 'CreditDungLettersController@getBranches')->name('dunning.branches');

//RAFFLE DRAW

Route::get('/addessa/raffle', 'RaffleDrawController@run');


Route::group(['prefix' => 'authorized', 'middleware' => ['jwt.auth', 'gift_code']], function () {
	//AUTHORIZED GIFT CODE
	Route::get('/giftcode', 'GiftCodeController@sync');
});
//GIFT CODE SYSTEM
Route::get('/sms/giftcode/sync', 'GiftCodeController@sync');
//GIFT CODE TRIGGER SEND
Route::get('/sms/giftcode/send', 'GiftCodeController@send');
//GIFT CODE LOGS 
Route::get('/sms/giftcode/fetch', 'GiftCodeLogsController@index');

//resync
 
Route::get('/re/sysnc', 'SapApiController@Rsync_branchsegment');
//Change Background
Route::get('/change/background', 'SapApiController@changecolor');
 



//PRINT PREVIEW SAP QUERIES
Route::get('/reports/printview', 'SapRportsController@preview')->name('sap.SapRports.crb.print');
//FETCH SERIES NAME
Route::get('/seriesname/fetch', 'SapRportsController@seriesname')->name('sap.SapRports.series');
//GET AND UPDATE DATABASE MSSQL FOR SAP CONNECTIONS SELECTIONS
Route::get('/connections', 'DatabaseSelectionController@connections')->name('sap.mssq.conn');
Route::post('/connections/update', 'DatabaseSelectionController@update')->name('sap.mssq.conn.update');


//DATABASE CONFIGURE SETTINGS
Route::group(['prefix' => '/settings', 'middleware' => ['jwt.auth', 'settings']], function () {
Route::post('/database/fetch', 'DatabaseSelectionController@fetchDB')->name('sap.database.configure.fetch');
Route::post('/database/create', 'DatabaseSelectionController@createDB')->name('sap.database.configure.create');
Route::post('/database/update', 'DatabaseSelectionController@updateDB')->name('sap.database.configure.update');
Route::post('/database/delete', 'DatabaseSelectionController@deleteDB')->name('sap.database.configure.delete');
Route::post('/database/testdb', 'DatabaseSelectionController@testDB')->name('sap.testdb.conn');
});

 
Route::get('/collection/queries/sync', 'CollectionQueriesController@sync');
Route::get('/collection/queries/sync_schedule', 'CollectionQueriesController@sync_schedule');
 
Route::get('/collection/schedule/index', 'CollectionQueriesController@schedule_index');
Route::post('/collection/schedule/set', 'CollectionQueriesController@schedule_set');
Route::post('/collection/payment/set', 'CollectionQueriesController@payment_store');
Route::get('/collection/collected/payments', 'CollectionQueriesController@collected_payments_index');
Route::get('/collection/scheduled/today', 'CollectionQueriesController@scheduled_today');

Route::post('/collection/arrived/set', 'CollectionQueriesController@gpstrack');

 
Route::get('/collection/track/get', 'CollectionQueriesController@getTrack');
