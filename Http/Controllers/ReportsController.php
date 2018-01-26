<?php

namespace Ignite\Reports\Http\Controllers;

use Ignite\Core\Http\Controllers\AdminBaseController;

use Carbon\Carbon;
use Ignite\Evaluation\Entities\Visit;

class ReportsController extends AdminBaseController
{
    protected $clientRepository;

    /*
    * Inject the dependencies into the class
    */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Display a listing of the resource.
     */
    public function hypertension()
    {
        $visits = $this->getVisitsBasedOnDate();

        $start = session('hbdStart');

        $end = session('hbdEnd');

        return view('reports::reports.hypertension', compact('start', 'end', 'visits'));
    }

    public function getVisitsBasedOnDate()
    {
        session()->forget(['hbdStart', 'hbdEnd']);

        $search = ['htn', 'hypertension', 'dm', 'diabetes'];

        $visits = Visit::whereHas('notes', function ($query) use ($search){
            foreach ($search as $index => $item) {
                $index == 0 ? $query->where('diagnosis', 'like', "%$item%")

                        : $query->orWhere('diagnosis', 'like', "%$item%");
            }
        })->with(['notes', 'vitals']);

        if(!is_null(request('start')) or !is_null(request('end')))
        {
            $hbdStart = is_null(request('start')) ? Carbon::parse('first day of January 2017') : Carbon::parse(request('start'))->startOfDay();

            $hbdEnd = Carbon::parse(request('end'))->endOfDay();

            session(compact('hbdStart', 'hbdEnd'));

            return $visits->whereDate('created_at', '>=', session('hbdStart'))
                          ->whereDate('created_at', '<=', session('hbdEnd'))
                          ->get();
        }

        return $visits->get();
    }
}
