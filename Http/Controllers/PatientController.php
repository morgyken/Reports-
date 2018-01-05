<?php

namespace Ignite\Reports\Http\Controllers;

use Carbon\Carbon;
use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Evaluation\Entities\DoctorNotes;
use Ignite\Evaluation\Entities\Investigations;
use Ignite\Evaluation\Entities\Prescriptions;
use Ignite\Evaluation\Entities\Visit;
use Ignite\Evaluation\Entities\Vitals;
use Ignite\Reception\Entities\Patients;
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
                    $date = Carbon::parse($request->end)->endOfDay()->toDateTimeString();
                    $diagnoses->where('created_at', '<=', $date);
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

    public function clinic(Request $request, $clinic)
    {

        $_c = [
            'mch' => 'MCH',
            'hpd' => 'Hypertension and Diabetes',
            'orthopeadic' => 'Orthopeadic',
            'popc' => 'Pedeatrics',
            'mopc' => 'Medical',
            'sopc' => 'Sergical',
            'gopc' => 'Gyenecology',
            'physio' => 'Physiotherapy',
        ];
        $this->data['clinic'] = $_c[$clinic];
        $this->data['filter'] = null;
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
                    $date = Carbon::parse($request->end)->endOfDay()->toDateTimeString();
                    $diagnoses->where('created_at', '<=', $date);
                    $this->data['filter']['to'] = (new Date($request->end))->format('jS M Y');
                }
            }
            $this->data['diagnoses'] = $diagnoses->whereNotNull('diagnosis')->get();
        } else {
            $this->data['diagnoses'] = DoctorNotes::whereNotNull('diagnosis')
                ->orderBy('created_at', 'asc')
                ->whereHas('visits.destinations', function (Builder $query) use ($clinic) {
                    $query->where('department', ucfirst($clinic));
                })
                ->get();
        }
        return view('reports::patients.clinic', ['data' => $this->data]);
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

    public function contacts()
    {
        $this->data['patients'] = Patients::all();
        //Charts::visitCharts($this->data['visits']);
        return view('reports::patients.patients', ['data' => $this->data]);
    }

    public function hpdReport()
    {
        $rangeStart = Carbon::createFromDate(2017, 12, 1);

        $rangeEnd = Carbon::createFromDate(2017, 12, 31);

        $visits = Visit::whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->whereHas('notes', function (Builder $query) {
                $query->whereNotNull('diagnosis');
                $query->where(function (Builder $query) {
                    $search = ['htn', 'hypertension', 'dm', 'diabetes'];
                    foreach ($search as $like) {
                        $query->orWhere('diagnosis', 'like', '%' . $like . '%');
                    }
                });

            })
            ->get();
        $visits = $visits->transform(function ($visit) {

            $patient = $visit->patients;

            $diagnosis = $visit->notes->diagnosis;

            $prescriptions = getPrescriptions($visit->prescriptions);

            return [
                'visit_date' => Carbon::parse($visit->created_at)->toDateTimeString(),

                'patient_id' => $patient->patient_no,

                'patient_name' => $patient->fullName,

                'phone_number' => $patient->mobile,

                'age' => $patient->age,

                'gender' => $patient->sex,

                'residence' => $patient->town,

                'visit_type' => getVisitType($visit),

                'bp_systolic' => $visit->vitals->bp_systolic ?? $this->anyVitals($visit, 'bp_systolic'),

                'bp_diastolic' => $visit->vitals->bp_diastolic ?? $this->anyVitals($visit, 'bp_diastolic'),

                'weight' => $visit->vitals ? $visit->vitals->weight : '',

                'diagnosis' => $diagnosis,

                'treatment' => $prescriptions
            ];

        })->toArray();
        generateLabsReport($visits);

        dd("done");
    }

    private function anyVitals(Visit $visit, $vital)
    {
        $patient_visits = Visit::wherePatient($visit->patient)->latest()->get();
        foreach ($patient_visits as $v) {
            $_v = @$v->vitals->{$vital};
            if (!empty($_v)) {
                return $_v;
            }
        }
        return '';
    }
}
