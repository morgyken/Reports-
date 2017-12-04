<?php

use Illuminate\Routing\Router;

/** @var  Router $router */

$router->get('/', 'ReportsController@index');

$router->get('procedures/performed', ['uses' => 'PatientController@procedures', 'as' => 'patients.procedures']);
$router->any('procedures/treatment', ['uses' => 'PatientController@treatment', 'as' => 'patients.treatment']);
$router->get('medication/given', ['uses' => 'PatientController@medication', 'as' => 'patients.medication']);
$router->get('patient/visits', ['uses' => 'PatientController@visits', 'as' => 'patients.visits']);


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

$router->get('/hyper-tension', function(){
    

    $visits = \Ignite\Evaluation\Entities\Visit::whereMonth('created_at', 11)->get();



    //get the visits doctors notes
    $visits = $visits->filter(function($visit){

        //get the patients diagnosis
        $search = ['htn', 'hypertension', 'dm', 'diabetes'];

        if($visit->notes)
        {
            $diagnosis = $visit->notes->diagnosis;
            
            foreach($search as $item)
            {
                return (strpos($diagnosis, $item) !== false);
            }
        }
    });

    $visits = $visits->transform(function($visit){
        
        $patient = $visit->patients;

        $vitals = $visit->vitals;

        $diagnosis = $visit->notes->diagnosis;

        $prescriptions = getPrescriptions($visit->prescriptions);

        return [
            'visit_date' => \Carbon\Carbon::parse($visit->created_at)->toDateTimeString(),

            'patient_id' => $patient->patient_no,

            'patient_name' => $patient->fullName,

            'phone_number' => $patient->mobile,

            'age' => $patient->age,

            'gender' => $patient->sex,

            'residence' => $patient->town,

            'visit_type' => '',

            'bp_systolic' => $vitals->bp_systolic,

            'bp_diastolic' => $vitals->bp_diastolic,

            'weight' => $vitals->weight,

            'diagnosis' => $diagnosis,

            'treatment' => $prescriptions
        ];


    })->toArray();

    generateLabsReport($visits);

    dd("done");
});


function getPrescriptions($prescriptions)
{
    $data = "";

    $prescriptions->each(function($prescription) use(&$data){

        $prescription->load('drugs');

        $data .= $prescription->drugs->name . ", ";
    });

    return trim($data, ', ');
}




/*
* Generates a lab report and downloads it to an excel
*/
function generateLabsReport($visits)
{
    ob_clean();

    \Excel::create('hypertension_diabetes', function($excel) use($visits){

        $excel->sheet('hypertension_diabetes', function($sheet) use($visits){

            $sheet->row(1, ['Visit date', 'Patient ID', 'Patient Name', 'Phone', 'Age', 'Gender', 'Residence', 'Visit Type', 'Bp Systolic', 'Bp Diastolic', 'Weight', 'Diagnosis', 'Treatment']);

            $sheet->freezeFirstRow();

            foreach($visits as $report)
            {
                $sheet->appendRow([
                    $report['visit_date'],
                    $report['patient_id'],
                    $report['patient_name'],
                    $report['phone_number'],
                    $report['age'],
                    $report['gender'],
                    $report['residence'],
                    $report['visit_type'],
                    $report['bp_systolic'],
                    $report['bp_diastolic'],
                    $report['weight'],
                    trim($report['diagnosis']),
                    $report['treatment'],
                ]);
            }

        });

    })->export('xls');
}
