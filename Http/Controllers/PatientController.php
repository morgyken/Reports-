<?php

namespace Ignite\Reports\Http\Controllers;

use Carbon\Carbon;
use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Evaluation\Entities\DoctorNotes;
use Ignite\Evaluation\Entities\Investigations;
use Ignite\Evaluation\Entities\Prescriptions;
use Ignite\Evaluation\Entities\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class PatientController extends AdminBaseController
{
    /**
     * Filter procedures
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function procedures()
    {
        $this->data['investigations'] = Investigations::where('type', '<>', 'treatment')->get();
        return view('reports::patients.procedures', ['data' => $this->data]);
    }

    public function treatment(Request $request)
    {
        $this->data['filter'] = null;
        $this->data['diagnoses'] = DoctorNotes::whereNotNull('diagnosis')->orderBy('created_at', 'desc')
            ->get();
        if ($request->isMethod('post')) {
            $diagnoses = DoctorNotes::query();

            if (($request->has('end') && $request->has('start')) && ($request->end == $request->start)) {
                $diagnoses->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new Date($request->end))->format('jS M Y');
            } else {
                if ($request->has('start')) {
                    $diagnoses->where('created_at', '>=', $request->start);
                    $this->data['filter']['from'] = (new Date($request->start))->format('jS M Y');
                }
                if ($request->has('end')) {
                    $diagnoses->where('created_at', '<=', $request->end);
                    $this->data['filter']['to'] = (new Date($request->end))->format('jS M Y');
                }
            }
            if ($request->has('uo')) {

                if ($request->uo == 'u') {
                    $diagnoses->whereHas('visits.patients', function (Builder $builder) {
                        $date = Carbon::now();
                        $builder->where('dob', '>', $date->subYears(5)->toDateString());
                    });

                } else if ($request->uo == 'o') {
                    $diagnoses->whereHas('visits.patients', function (Builder $builder) {
                        $date = Carbon::now();
                        $builder->where('dob', '<', $date->subYears(5)->toDateString());
                    });
                }
            }
            $this->data['diagnoses'] = $diagnoses->whereNotNull('diagnosis')->get();
        }
        return view('reports::patients.treatment', ['data' => $this->data]);
    }

    public function hpd(Request $request)
    {
        $this->data['filter'] = null;
        $this->data['diagnoses'] = DoctorNotes::whereNotNull('diagnosis')
            ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->subMonth()->endOfMonth()])
            ->orderBy('created_at', 'asc')
            ->where(function (Builder $query) {
                $query->orWhere('diagnosis', 'like', '%htn%');
                $query->orWhere('diagnosis', 'like', '%dm%');
                $query->orWhere('diagnosis', 'like', '%hypertension%');
                $query->orWhere('diagnosis', 'like', '%diabetes%');
            })
            ->get();

        if ($request->isMethod('post')) {
            $diagnoses = DoctorNotes::query();

            if (($request->has('end') && $request->has('start')) && ($request->end == $request->start)) {
                $diagnoses->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new Date($request->end))->format('jS M Y');
            } else {
                if ($request->has('start')) {
                    $diagnoses->where('created_at', '>=', $request->start);
                    $this->data['filter']['from'] = (new Date($request->start))->format('jS M Y');
                }
                if ($request->has('end')) {
                    $diagnoses->where('created_at', '<=', $request->end);
                    $this->data['filter']['to'] = (new Date($request->end))->format('jS M Y');
                }
            }
            if ($request->has('uo')) {

                if ($request->uo == 'u') {
                    $diagnoses->whereHas('visits.patients', function (Builder $builder) {
                        $date = Carbon::now();
                        $builder->where('dob', '>', $date->subYears(5)->toDateString());
                    });

                } else if ($request->uo == 'o') {
                    $diagnoses->whereHas('visits.patients', function (Builder $builder) {
                        $date = Carbon::now();
                        $builder->where('dob', '<', $date->subYears(5)->toDateString());
                    });
                }
            }
            $this->data['diagnoses'] = $diagnoses->whereNotNull('diagnosis')->get();
        }
        return view('reports::patients.hpd', ['data' => $this->data]);
    }

    public function medication()
    {
        $this->data['medication'] = Prescriptions::all();
        return view('reports::patients.medicine', ['data' => $this->data]);
    }

    public function visits()
    {
        $this->data['visits'] = Visit::all();
        //Charts::visitCharts($this->data['visits']);
        return view('reports::patients.visits', ['data' => $this->data]);
    }

}
