<?php

namespace Ignite\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ignite\Reports\Repositories\ClientRepository;
use Ignite\Core\Http\Controllers\AdminBaseController;

class ClientDoctorsController extends AdminBaseController
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

        $clients = collect($clients)->filter(function($item, $index){

            return $index == 'Doctor';

        })['Doctor'];

        $clients = $clients->groupBy('user')->map(function($item, $index){

            $user = \Ignite\Users\Entities\User::find($index);

            $doctor = $user ? $user->profile->fullName : '';

            return [

                'doctor' => $doctor,

                'total' => count($item)
            ];

        });

        return view('reports::clients.doctors', [
            'clients' => $clients, 
            'dateFilters' => $dateFilters,
        ]);
    }
}
