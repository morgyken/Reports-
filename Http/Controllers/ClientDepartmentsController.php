<?php

namespace Ignite\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ignite\Reports\Repositories\ClientRepository;
use Ignite\Core\Http\Controllers\AdminBaseController;

class ClientDepartmentsController extends AdminBaseController
{
    protected $clientRepository;

    /*
    * Inject the dependencies into the class
    */
    public function __construct(ClientRepository $clientRepository)
    {
        parent::__construct();

        $this->clientRepository = $clientRepository;
    }

    /*
     * Display a listing of the resource.
     */
    public function index()
    {   
        $filters = request()->get('filters');

        $clients = $this->clientRepository->getFilteredClients($filters);

        $dateFilters = $this->clientRepository->getDateFilters();

        $total = 0;

        foreach($clients as $client)
        {
            $total += count($client);
        }

        return view('reports::clients.departments', [
            'clients' => $clients, 
            'total' => $total, 
            'dateFilters' => $dateFilters,
        ]);
    }
}
