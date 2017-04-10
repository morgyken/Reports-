<?php

namespace Ignite\Reports\Http\Controllers;

use Ignite\Evaluation\Entities\Investigations;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ignite\Finance\Entities\EvaluationPayments;

class ApiController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function GetDiagnoses() {
        if ($this->request->type == 'clinic') {
            $payments = EvaluationPayments::whereHas('details', function($query) {
                        $query->whereHas('investigations', function($q) {
                            $q->whereType('treatment')
                                    ->whereHas('visits', function($query) {
                                        $query->whereHas('clinics', function($query) {
                                            $query->whereId($this->request->value);
                                        });
                                    });
                        });
                    })->get();
        } else {
            $payments = EvaluationPayments::whereHas('details', function($query) {
                        $query->whereHas('investigations', function($q) {
                            $q->whereType('treatment')
                                    ->whereHas('doctors', function($query) {
                                        $query->whereId($this->request->value);
                                    });
                        });
                    })->get();
        }

        foreach ($payments as $payment) {
            if ($payment->visits->doctor !== "") {
                echo "
                <tr>
                    <td>" . $payment->id . "</td>
                    <td>" . $payment->receipt . "</td>
                    <td>" . $payment->visits->clinics->name . "</td>
                    <td>" . $payment->visits->doctor . "</td>
                    <td>" . $payment->patients->full_name . "</td>
                    <td>" . smart_date_time($payment->created_at) . "</td>
                    <td>" . $payment->total . "</td>
                    <td>" . $payment->modes . "</td>
                </tr>";
            }
        }
    }

}
