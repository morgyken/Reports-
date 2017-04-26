<?php

namespace Ignite\Reports\Http\Controllers;

use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Finance\Entities\PaymentsCard;
use Ignite\Finance\Entities\PaymentsCash;
use Ignite\Finance\Entities\PaymentsCheque;
use Ignite\Finance\Entities\PaymentsMpesa;
use Ignite\Finance\Entities\EvaluationPayments;
use Ignite\Reception\Entities\Patients;
use Ignite\Users\Entities\Roles;
use Ignite\Evaluation\Entities\Investigations;
use Ignite\Settings\Entities\Clinics;
use Illuminate\Http\Request;
use Ignite\Finance\Entities\InsuranceInvoicePayment;
use Ignite\Finance\Entities\InsuranceInvoice;

class FinanceController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Filter procedures
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function debtors(Request $request, $patient = null) {
        if (!empty($patient)) {
            $this->data['patient'] = Patients::find($patient);
            return view('reports::finance.person_debt')->with('data', $this->data);
        }

        $this->data['records'] = \Collabmed\Model\Evaluation\PatientTreatment::whereIsPaid(false)
                        ->groupBy('visit')->select('visit', DB::raw('SUM(price*no_performed) as total'))->get();

        return view('reports::finance.debtors')->with('data', $this->data);
    }

    public function per_doctor(Request $request) {
        $this->data['filter'] = null;
        if ($request->isMethod('post')) {
            $temp_cash = PaymentsCash::query();
            $temp_card = PaymentsCard::query();
            $temp_cheque = PaymentsCheque::query();
            $temp_mpesa = PaymentsMpesa::query();
            $temp_insurance = InsuranceInvoice::query();
            if ($request->has('start')) {
                $temp_cash->where('created_at', '>=', $request->start);
                $temp_card->where('created_at', '>=', $request->start);
                $temp_cheque->where('created_at', '>=', $request->start);
                $temp_mpesa->where('created_at', '>=', $request->start);
                $temp_insurance->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }
            if ($request->has('end')) {
                $temp_cash->where('created_at', '<=', $request->end);
                $temp_card->where('created_at', '<=', $request->end);
                $temp_cheque->where('created_at', '<=', $request->end);
                $temp_mpesa->where('created_at', '<=', $request->end);
                $temp_insurance->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }

            //For specific date
            if ($request->has('date')) {
                $temp_cash->where('created_at', '==', $request->date);
                $temp_card->where('created_at', '==', $request->date);
                $temp_cheque->where('created_at', '==', $request->date);
                $temp_mpesa->where('created_at', '==', $request->date);
                $temp_insurance->where('created_at', '==', $request->date);
                $this->data['filter']['date'] = (new \Date($request->date))->format('jS M Y');
            }

            $this->query_payments_doctor(
                    $request, $temp_cash, $temp_card, $temp_cheque, $temp_mpesa, $temp_insurance
            );
        } else {
            $this->get_all_payments_doctor();
        }

        $this->data['doctors'] = \Ignite\Users\Entities\User::whereHas('roles', function($query) {
                    $query->whereRole_id(5);
                })->get();

        return view('reports::finance.per_doctor')->with('data', $this->data);
    }

    public function query_payments_doctor(Request $request, $temp_cash, $temp_card, $temp_cheque, $temp_mpesa, $temp_insurance) {
        $this->fetch_for_doctor_x($request, $temp_cash, $temp_card, $temp_cheque, $temp_mpesa, $temp_insurance);

        $this->data['cash'] = $temp_cash->whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['card'] = $temp_card->whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['cheque'] = $temp_cheque->whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['mpesa'] = $temp_mpesa->whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();
        $this->data['insurance'] = $temp_insurance->get();
    }

    public function fetch_for_doctor_x($request, $temp_cash, $temp_card, $temp_cheque, $temp_mpesa, $temp_insurance) {
        if ($request->has('medic')) {
            session(['medic' => $request->medic]);
            $temp_card->whereHas('payments', function($q) {
                $q->whereHas('visits', function($query) {
                    $query->whereHas('investigations', function($_query) {
                        $_query->whereType('treatment');
                        $_query->whereHas('doctors', function ($q2) {
                            $q2->whereId(\Session::get('medic'));
                        });
                    });
                });
            })->get();


            $temp_cash->whereHas('payments', function($q) {
                $q->whereHas('visits', function($query) {
                    $query->whereHas('investigations', function($_query) {
                        $_query->whereType('treatment');
                        $_query->whereHas('doctors', function ($q2) {
                            $q2->whereId(\Session::get('medic'));
                        });
                    });
                });
            })->get();

            $temp_mpesa->whereHas('payments', function($q) {
                $q->whereHas('visits', function($query) {
                    $query->whereHas('investigations', function($_query) {
                        $_query->whereType('treatment');
                        $_query->whereHas('doctors', function ($q2) {
                            $q2->whereId(\Session::get('medic'));
                        });
                    });
                });
            })->get();

            $this->data['doc'] = $request->medic;
            $temp_insurance->whereHas('visits', function($q) {
                $q->whereHas('investigations', function($query) {
                    $query->whereType('treatment');
                    $query->whereHas('doctors', function ($q2) {
                        $q2->whereId(\Session::get('medic'));
                    });
                });
            })->get();

            $temp_cheque->whereHas('payments', function($q) {
                $q->whereHas('visits', function($query) {
                    $query->whereHas('investigations', function($_query) {
                        $_query->whereType('treatment');
                        $_query->whereHas('doctors', function ($q2) {
                            $q2->whereId(\Session::get('medic'));
                        });
                    });
                });
            })->get();
        }
    }

    public function get_all_payments_doctor() {
        $this->data['cash'] = PaymentsCash::whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('destinations', function ($__query) {
                            $__query->whereDepartment('Doctor');
                        })->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment')
                                    ->whereHas('doctors', function ($q2) {
                                        //$q2->whereId(\Session::get('medic'));
                                    });
                        });
                    });
                })->get();

        $this->data['card'] = PaymentsCard::whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['cheque'] = PaymentsCheque::whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['mpesa'] = PaymentsMpesa::whereHas('payments', function($q) {
                    $q->whereHas('visits', function($query) {
                        $query->whereHas('investigations', function($_query) {
                            $_query->whereType('treatment');
                            $_query->whereHas('doctors', function ($q2) {
                                //$q2->whereId(\Session::get('medic'));
                            });
                        });
                    });
                })->get();

        $this->data['insurance'] = InsuranceInvoice::all();
    }

    public function cashier(Request $request) {
        $this->data['filter'] = null;
        if ($request->isMethod('post')) {
            $temp_cash = PaymentsCash::query();
            $temp_card = PaymentsCard::query();
            $temp_cheque = PaymentsCheque::query();
            $temp_mpesa = PaymentsMpesa::query();
            $temp_insurance = InsuranceInvoice::query();

            if ($request->has('start')) {
                $temp_cash->where('created_at', '>=', $request->start);
                $temp_card->where('created_at', '>=', $request->start);
                $temp_cheque->where('created_at', '>=', $request->start);
                $temp_mpesa->where('created_at', '>=', $request->start);
                $temp_insurance->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }
            if ($request->has('end')) {
                $temp_cash->where('created_at', '<=', $request->end);
                $temp_card->where('created_at', '<=', $request->end);
                $temp_cheque->where('created_at', '<=', $request->end);
                $temp_mpesa->where('created_at', '<=', $request->end);
                $temp_insurance->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }


            if ($request->has('date')) {
                $temp_cash->where('created_at', '==', $request->date);
                $temp_card->where('created_at', '==', $request->date);
                $temp_cheque->where('created_at', '==', $request->date);
                $temp_mpesa->where('created_at', '==', $request->date);
                $temp_insurance->where('created_at', '==', $request->date);
                $this->data['filter']['for'] = (new \Date($request->date))->format('jS M Y');
            }

            $this->data['cash'] = $temp_cash->get();
            $this->data['card'] = $temp_card->get();
            $this->data['cheque'] = $temp_cheque->get();
            $this->data['mpesa'] = $temp_mpesa->get();
            $this->data['insurance'] = $temp_insurance->get();
        } else {
            $this->data['cash'] = PaymentsCash::all();
            $this->data['card'] = PaymentsCard::all();
            $this->data['cheque'] = PaymentsCheque::all();
            $this->data['mpesa'] = PaymentsMpesa::all();
            $this->data['insurance'] = InsuranceInvoice::all();
        }

        return view('reports::finance.cashier')->with('data', $this->data);
    }

    public function payment_mode(Request $request) {
        $this->data['filter'] = null;
        if ($request->isMethod('post')) {
            $temp_cash = PaymentsCash::query();
            $temp_card = PaymentsCard::query();
            $temp_cheque = PaymentsCheque::query();
            $temp_mpesa = PaymentsMpesa::query();
            $temp_insurance = InsuranceInvoice::query();
            if ($request->has('start')) {
                //$temp->where('created_at', '>=', $request->start);
                $temp_card->where('created_at', '>=', $request->start);
                $temp_cheque->where('created_at', '>=', $request->start);
                $temp_mpesa->where('created_at', '>=', $request->start);
                $temp_insurance->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }
            if ($request->has('end')) {
                //$temp->where('created_at', '<=', $request->end);
                $temp_card->where('created_at', '<=', $request->end);
                $temp_cheque->where('created_at', '<=', $request->end);
                $temp_mpesa->where('created_at', '<=', $request->end);
                $temp_insurance->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }


            if ($request->has('date')) {
                //$temp->where('created_at', '<=', $request->end);
                $temp_card->where('created_at', '==', $request->date);
                $temp_cheque->where('created_at', '==', $request->date);
                $temp_mpesa->where('created_at', '==', $request->date);
                $temp_insurance->where('created_at', '==', $request->date);
                $this->data['filter']['for'] = (new \Date($request->date))->format('jS M Y');
            }

            if ($request->has('mode')) {
                if ($request->mode != 'all') {
                    if ($request->mode == 'cash') {
                        $temp_cash->where('amount', '>', 0);
                        $this->data['cash'] = $temp_cash->get();
                    } elseif ($request->mode == 'card') {
                        $temp_card->where('amount', '>', 0);
                        $this->data['card'] = $temp_card->get();
                    } elseif ($request->mode == 'mpesa') {
                        $temp_mpesa->where('amount', '>', 0);
                        $this->data['mpesa'] = $temp_mpesa->get();
                    } elseif ($request->mode == 'cheque') {
                        $temp_cheque->where('amount', '>', 0);
                        $this->data['cheque'] = $temp_cheque->get();
                    } elseif ($request->mode == 'insurance') {
                        $this->data['mode_insurance'] = 1;
                        $this->data['insurance'] = $temp_insurance->get();
                    }
                    $this->data['filter']['mode'] = ucfirst($request->mode);
                }
            }
        } else {
            $this->data['cash'] = PaymentsCash::all();
            $this->data['card'] = PaymentsCard::all();
            $this->data['cheque'] = PaymentsCheque::all();
            $this->data['mpesa'] = PaymentsMpesa::all();
            $this->data['insurance'] = InsuranceInvoice::all();
            $this->data['all'] = 1;
        }
        return view('reports::finance.payment_mode')->with('data', $this->data);
    }

    public function medic(Request $request) {
        $this->data['filter'] = null;
        $this->data['clinics'] = Clinics::all();

        $this->data['doctors'] = \Ignite\Users\Entities\User::whereHas('roles', function($query) {
                    $query->whereRole_id(5);
                })
                ->get();

        if ($request->isMethod('post')) {
            $temp = Investigations::query()->whereHas('doctors')->whereHas('payments')
                    ->whereHas('visits');
            $temp_insurance = InsuranceInvoice::query();

            if ($request->has('start')) {
                $temp->where('created_at', '>=', $request->start);
                $temp_insurance->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }

            if ($request->has('end')) {
                $temp->where('created_at', '<=', $request->end);
                $temp_insurance->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }

            if ($request->end == $request->start) {
                $date = $request->start;
                //dd($date);
                //$temp->where('created_at', '==', $date);
                $temp->whereDate('created_at', '=', $date);
                $temp_insurance->where('created_at', '=', $date);
                $this->data['filter']['for'] = (new \Date($date))->format('jS M Y');
            }

            if ($request->has('medic')) {
                session(['medic' => $request->medic]);
                $temp->whereType('treatment')
                        ->whereHas('doctors', function($query) {
                            $query->whereId(\Session::get('medic'));
                        })
                        ->get();

                $this->data['doc'] = $request->medic;
                $temp_insurance->whereHas('visits', function($q) {
                    $q->whereHas('investigations', function($query) {
                        $query->whereType('treatment');
                        $query->whereHas('doctors', function ($q2) {
                            $q2->whereId(\Session::get('medic'));
                        });
                    });
                })->get();
            }

            if ($request->has('clinic')) {
                session(['clinic' => ucfirst($request->clinic)]);
                $temp->whereType('treatment')
                        ->whereHas('visits', function($query) {
                            $query->whereHas('clinics', function($query) {
                                $query->whereId(\Session::get('clinic'));
                            });
                        })->get();

                $temp_insurance->whereHas('visits', function($query) {
                    $query->whereHas('clinics', function($query1) {
                        $query1->whereId(\Session::get('clinic'));
                    });
                })->get();
            }

            if ($request->has('mode')) {
                if ($request->mode == 'cash') {
                    $this->data['mode'] = 'cash';
                    $this->data['investigations'] = $temp->get();
                } elseif ($request->mode == 'insurance') {
                    $this->data['mode'] = 'insurance';
                    $this->data['insurance'] = $temp_insurance->get();
                }
                $this->data['filter']['mode'] = ucfirst($request->mode);
            }

            $this->data['investigations'] = $temp->get();
            $this->data['insurance'] = $temp_insurance->get();
        } else {
            $this->data['investigations'] = Investigations::whereType('treatment')
                    ->whereHas('doctors')
                    ->whereHas('payments')
                    ->whereHas('visits')
                    ->get();
            $this->data['insurance'] = InsuranceInvoice::all(); //whereHas('payments')->get();
            // dd($this->data['insurance']);
        }

        return view('reports::finance.doctor', ['data' => $this->data]);
    }

    public function department(Request $request) {
        $this->data['filter'] = null;
        $this->data['clinics'] = Clinics::all();
        if ($request->isMethod('post')) {
            try {
                $temp = Investigations::query();
                $temp_insurance = InsuranceInvoice::query();
                if ($request->has('start')) {
                    $temp->where('created_at', '>=', $request->start);
                    $temp_insurance->where('created_at', '>=', $request->start);
                    $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
                }

                if ($request->has('end')) {
                    $temp->where('created_at', '<=', $request->end);
                    $temp_insurance->where('created_at', '<=', $request->end);
                    $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
                }

                if ($request->has('date')) {
                    $temp->where('created_at', '==', $request->date);
                    $temp_insurance->where('created_at', '==', $request->date);
                    $this->data['filter']['For'] = (new \Date($request->date))->format('jS M Y');
                }

                if ($request->has('department')) {
                    if ($request->department == 'physio') {
                        session(['department' => 'Physiotherapy']);
                    } elseif ($request->department == 'laboratory') {
                        session(['department' => 'Lab']);
                    } else {
                        session(['department' => ucfirst($request->department)]);
                    }
                    $temp->whereHas('procedures', function($query) {
                        $query->whereHas('categories', function($query2) {
                            $query2->whereName(\Session::get('department'));
                        });
                    });

                    $temp_insurance->whereHas('visits', function($q) {
                        $q->whereHas('investigations', function($query) {
                            $query->whereHas('procedures', function ($q2) {
                                $q2->whereName(\Session::get('department'));
                            });
                        });
                    });

                    $this->data['department'] = ucfirst($request->department);
                }

                if ($request->has('mode')) {
                    if ($request->mode == 'cash') {
                        $this->data['mode'] = 'cash';
                        $temp_cheque->where('amount', '>', 0);
                        $this->data['cheque'] = $temp_cheque->get();
                        $temp_cash->where('amount', '>', 0);
                        $this->data['cash'] = $temp_cash->get();
                        $temp_card->where('amount', '>', 0);
                        $this->data['card'] = $temp_card->get();
                        $temp_mpesa->where('amount', '>', 0);
                        $this->data['mpesa'] = $temp_mpesa->get();
                    } elseif ($request->mode == 'insurance') {
                        $this->data['mode'] = 'insurance';
                        $this->data['insurance'] = $temp_insurance->get();
                    }
                    $this->data['filter']['mode'] = ucfirst($request->mode);
                }


                if ($request->has('clinic')) {
                    session(['clinic' => ucfirst($request->clinic)]);
                    $temp->whereHas('visits', function($query) {
                                $query->whereHas('clinics', function($query) {
                                    $query->whereId(\Session::get('clinic'));
                                });
                            })
                            ->get();
                }
                $this->data['investigations'] = $temp->get();
                $this->data['insurance'] = $temp_insurance->get();
            } catch (\Exception $ex) {
                $this->data['investigations'] = Investigations::all();
                $this->data['insurance'] = InsuranceInvoice::all();
            }
        } else {
            $this->data['investigations'] = Investigations::all();
            $this->data['insurance'] = InsuranceInvoice::all();
        }
        return view('reports::finance.department', ['data' => $this->data]);
    }

    public function viaInsurance(Request $request) {
        $this->data['filter'] = null;
        $this->data['clinics'] = Clinics::all();

        $this->data['medic'] = Roles::where('slug', '=', 'doctor')
                ->orWhere('slug', '=', 'nurse')
                ->get();

        if ($request->isMethod('post')) {
            $temp = InsuranceInvoicePayment::query();

            if ($request->has('start')) {
                session(['from' => ucfirst($request->start)]);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
                $temp->whereHas('invoice', function($query) {
                    $query->whereHas('visits', function($query2) {
                        $query2->whereHas('investigations', function($query3) {
                            $query3->where('created_at', '>=', \Session::get('from'));
                        });
                    });
                });
            }

            if ($request->has('end')) {
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
                session(['to' => ucfirst($request->end)]);
                $temp->whereHas('invoice', function($query) {
                    $query->whereHas('visits', function($query2) {
                        $query2->whereHas('investigations', function($query3) {
                            $query3->where('created_at', '<=', \Session::get('to'));
                        });
                    });
                });
            }


            if ($request->has('date')) {
                $this->data['filter']['for'] = (new \Date($request->date))->format('jS M Y');
                session(['date' => ucfirst($request->date)]);
                $temp->whereHas('invoice', function($query) {
                    $query->whereHas('visits', function($query2) {
                        $query2->whereHas('investigations', function($query3) {
                            $query3->where('created_at', '==', \Session::get('date'));
                        });
                    });
                });
            }



            if ($request->has('department')) {
                if ($request->department == 'physio') {
                    session(['department' => 'Physiotherapy']);
                } elseif ($request->department == 'laboratory') {
                    session(['department' => 'Lab']);
                } else {
                    session(['department' => ucfirst($request->department)]);
                }

                $temp->whereHas('invoice', function($query) {
                    $query->whereHas('visits', function($query2) {
                        $query2->whereHas('investigations', function($query3) {
                            $query3->whereHas('procedures', function($query4) {
                                $query4->whereHas('categories', function($query5) {
                                    $query5->whereName(\Session::get('department'));
                                });
                            });
                        });
                    });
                });
                $this->data['department'] = ucfirst($request->department);
            }

            if ($request->has('medic')) {
                session(['medic' => ucfirst($request->medic)]);

                $temp->whereHas('invoice', function($query) {
                    $query->whereHas('visits', function($query2) {
                        $query2->whereHas('investigations', function($query3) {
                            $query3->whereType('treatment')
                                    ->whereHas('doctors', function($query) {
                                        $query->whereId(\Session::get('medic'));
                                    });
                        });
                    });
                });
            }


            if ($request->has('clinic')) {
                session(['clinic' => ucfirst($request->clinic)]);
                $temp->whereHas('invoice', function($q) {
                            $q->whereHas('visits', function($query) {
                                $query->whereHas('clinics', function($query) {
                                    $query->whereId(\Session::get('clinic'));
                                });
                            });
                        })
                        ->get();
            }
            $this->data['i_payments'] = $temp->get();
        } else {
            $this->data['i_payments'] = InsuranceInvoicePayment::all();
        }
        return view('reports::finance.insurance', ['data' => $this->data]);
    }

    function filter_description(array $data = null) {
        $text = "Showing records ";
        if (empty($data['from']) && empty($data['to'])) {
            $text = "Showing all records available";
        }
        if (!empty($data['from']))
            $text.=" from " . $data['from'];
        if (!empty($data['to']))
            $text.=" up to " . $data['to'];
        if (!empty($data['mode']))
            $text.=". Payment mode " . ucfirst($data['mode']);
        if (!empty($data['department']))
            $text.=". Department: " . ucfirst($data['department']);
        return $text;
    }

}
