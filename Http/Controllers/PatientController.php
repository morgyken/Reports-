<?php

namespace Ignite\Reports\Http\Controllers;

use Ignite\Reports\Library\Charts;
use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Evaluation\Entities\Investigations;
use Ignite\Evaluation\Entities\Prescriptions;
use Ignite\Evaluation\Entities\Visit;
Use Ignite\Settings\Entities\Clinics;
use Ignite\Users\Entities\UserProfile;

class PatientController extends AdminBaseController {

    /**
     * Filter procedures
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function procedures() {
        $this->data['investigations'] = Investigations::where('type', '<>', 'treatment')->get();
        Charts::procedureCharts($this->data['investigations']);
        return view('reports::patients.procedures', ['data' => $this->data]);
    }

    public function treatment() {
        $this->data['clinics'] = Clinics::all();
        $this->data['clinician'] = UserProfile::where('job_description', '=', 'Doctor')
                ->orWhere('job_description', '=', 'Nurse')
                ->get();
        $this->data['investigations'] = Investigations::whereType('treatment')->get();
        return view('reports::patients.treatment', ['data' => $this->data]);
    }

    public function medication() {
        $this->data['medication'] = Prescriptions::all();
        return view('reports::patients.medicine', ['data' => $this->data]);
    }

    public function visits() {
        $this->data['visits'] = Visit::all();
        Charts::visitCharts($this->data['visits']);
        return view('reports::patients.visits', ['data' => $this->data]);
    }

}
