<?php

namespace Ignite\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ignite\Reports\Repositories\LabRepository;
use Ignite\Core\Http\Controllers\AdminBaseController;

class LabController extends AdminBaseController
{
    protected $labRepository;

    /*
    * Inject the dependencies into the class
    */
    public function __construct(LabRepository $labRepository)
    {
        parent::__construct();

        $this->labRepository = $labRepository;
    }

    /*
     * Display a listing of the resource.
     */
    public function index()
    {   
        $filters = request()->get('filters');

        $ageFilters = isset($filters['age']) ? $filters['age'] : 'all';

        $investigations = $this->labRepository->getFilteredInvestigations($filters);

        $grouped = $this->labRepository->getTotalGrouped($investigations);

        $dateFilters = $this->labRepository->getDateFilters();

        return view('reports::labs.index', [
            'investigations' => $grouped, 'dateFilters' => $dateFilters,
            'ageFilters' => $ageFilters,
        ]);
    }

    /**
     * Create a report, download and store it.
     */
    public function create()
    {
        $investigations = $this->labRepository->getFilteredInvestigations(
            request()->get('filters')
        );

        $grouped = $this->labRepository->getTotalGrouped($investigations);

        $this->labRepository->generateLabsReport($grouped);
    }
}
