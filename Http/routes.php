<?php

use Illuminate\Routing\Router;

/** @var  Router $router */

$router->get('/', 'ReportsController@index');

$router->get('procedures/performed', ['uses' => 'PatientController@procedures', 'as' => 'patients.procedures']);
$router->any('procedures/treatment', ['uses' => 'PatientController@treatment', 'as' => 'patients.treatment']);
$router->any('procedures/clinic/{clinic}/reports', ['uses' => 'PatientController@clinic', 'as' => 'patients.clinic']);
$router->get('medication/given', ['uses' => 'PatientController@medication', 'as' => 'patients.medication']);
$router->get('patient/visits', ['uses' => 'PatientController@visits', 'as' => 'patients.visits']);
$router->get('patient/contacts', ['uses' => 'PatientController@contacts', 'as' => 'patients.contacts']);


$router->match(['post', 'get'], 'cashier', ['uses' => 'FinanceController@cashier', 'as' => 'finance.cashier']);
$router->get('procedures/{who?}', ['uses' => 'FinanceController@procedures', 'as' => 'finance.procedures']);
$router->get('debtors/{person?}', ['uses' => 'FinanceController@debtors', 'as' => 'finance.debtors']);
$router->match(['post', 'get'], 'payment_mode', ['uses' => 'FinanceController@payment_mode', 'as' => 'finance.payment_mode']);

$router->match(['get', 'post'], 'revenue/medic', ['uses' => 'FinanceController@medic', 'as' => 'finance.doctor']);
$router->match(['get', 'post'], 'revenue/sales', ['uses' => 'FinanceController@sales', 'as' => 'finance.sales']);
$router->match(['get', 'post'], 'revenue/daktari', ['uses' => 'FinanceController@per_doctor', 'as' => 'finance.per_doctor']);
$router->match(['get', 'post'], 'revenue/department', ['uses' => 'FinanceController@department', 'as' => 'finance.department']);
$router->match(['get', 'post'], 'revenue/insurance', ['uses' => 'FinanceController@viaInsurance', 'as' => 'finance.insurance']);
////$router->get('revenue/department', ['uses' => 'FinanceController@department', 'as' => 'finance.department']);
//print
$router->get('cashiersummary', ['uses' => 'FinanceController@printCashier', 'as' => 'print.cashier']);
//$router->get('')
//inventory
$router->match(['post', 'get'], 'sales/report', ['uses' => 'InventoryController@timePeriodSales', 'as' => 'inventory.sales']);
$router->match(['post', 'get'], 'product/sales/report', ['uses' => 'InventoryController@itemSales', 'as' => 'inventory.sales.product']);
$router->match(['post', 'get'], 'stocks', ['uses' => 'InventoryController@stocks', 'as' => 'inventory.stocks']);
$router->match(['post', 'get'], 'stock/movement', ['uses' => 'InventoryController@stockMovement', 'as' => 'inventory.stocks.movement']);
$router->match(['post', 'get'], 'stock/expiry', ['uses' => 'InventoryController@expiry', 'as' => 'inventory.stocks.expiry']);
$router->match(['post', 'get'], 'lab', ['uses' => 'LabController@index', 'as' => 'labs']);
$router->get('lab/create', ['uses' => 'LabController@create', 'as' => 'labs.create']);

$router->match(['post', 'get'], 'client/depatments', ['uses' => 'ClientDepartmentsController@index', 'as' => 'client.departments']);

$router->match(['post', 'get'], 'client/doctors', ['uses' => 'ClientDoctorsController@index', 'as' => 'client.doctors']);

$router->match(['post', 'get'], '/hypertension', ['uses' => 'ReportsController@hypertension', 'as' => 'patients.hypertension']);
