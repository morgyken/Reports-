<?php

Route::group(['middleware' => 'web', 'prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Ignite\Reports\Http\Controllers'], function () {
    Route::get('/', 'ReportsController@index');

    Route::get('procedures/performed', ['uses' => 'PatientController@procedures', 'as' => 'patients.procedures']);
    Route::any('procedures/treatement', ['uses' => 'PatientController@treatment', 'as' => 'patients.treatment']);
    Route::get('medication/given', ['uses' => 'PatientController@medication', 'as' => 'patients.medication']);
    Route::get('patient/visits', ['uses' => 'PatientController@visits', 'as' => 'patients.visits']);


    Route::match(['post', 'get'], 'cashier', ['uses' => 'FinanceController@cashier', 'as' => 'finance.cashier']);
    Route::get('procedures/{who?}', ['uses' => 'FinanceController@procedures', 'as' => 'finance.procedures']);
    Route::get('debtors/{person?}', ['uses' => 'FinanceController@debtors', 'as' => 'finance.debtors']);
    Route::match(['post', 'get'], 'payment_mode', ['uses' => 'FinanceController@payment_mode', 'as' => 'finance.payment_mode']);

    Route::match(['get', 'post'], 'revenue/medic', ['uses' => 'FinanceController@medic', 'as' => 'finance.doctor']);
    Route::match(['get', 'post'], 'revenue/sales', ['uses' => 'FinanceController@sales', 'as' => 'finance.sales']);
    Route::match(['get', 'post'], 'revenue/daktari', ['uses' => 'FinanceController@per_doctor', 'as' => 'finance.per_doctor']);
    Route::match(['get', 'post'], 'revenue/department', ['uses' => 'FinanceController@department', 'as' => 'finance.department']);
    Route::match(['get', 'post'], 'revenue/insurance', ['uses' => 'FinanceController@viaInsurance', 'as' => 'finance.insurance']);
    ////Route::get('revenue/department', ['uses' => 'FinanceController@department', 'as' => 'finance.department']);
    //print
    Route::get('cashiersummary', ['uses' => 'FinanceController@printCashier', 'as' => 'print.cashier']);
    //Route::get('')
    //inventory
    Route::match(['post', 'get'], 'sales/report', ['uses' => 'InventoryController@timePeriodSales', 'as' => 'inventory.sales']);
    Route::match(['post', 'get'], 'product/sales/report', ['uses' => 'InventoryController@itemSales', 'as' => 'inventory.sales.product']);
    Route::match(['post', 'get'], 'stocks', ['uses' => 'InventoryController@stocks', 'as' => 'inventory.stocks']);
    Route::match(['post', 'get'], 'stock/movement', ['uses' => 'InventoryController@stockMovement', 'as' => 'inventory.stocks.movement']);
    Route::match(['post', 'get'], 'stock/expiry', ['uses' => 'InventoryController@expiry', 'as' => 'inventory.stocks.expiry']);
});
