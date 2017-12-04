<?php
namespace Ignite\Reports\Repositories;

use Ignite\Evaluation\Entities\Investigations;
use Ignite\Reports\Library\DateFilterTrait;
use Ignite\Evaluation\Entities\VisitDestinations;

/*
Lab repository that sits between eloquent and the LabController
*/
class ClientRepository
{
	use DateFilterTrait;

	/*
	* Get all the investigations in the table
	*/
	public function all()
	{
		return VisitDestinations::with(['visits', 'medics', 'room'])->get();
	}	

	/*
	* Filter the investigations between two given dates
	*/
	public function getFilteredByDate($dateFilters)
	{
		$start = trim($dateFilters['start']);
		$end = trim($dateFilters['end']);
        $relations = ['visits', 'medics', 'room'];

		if(!empty($start) and !empty($end))
		{
			return VisitDestinations::with($relations)
								 ->whereBetween($this->column, array_values($dateFilters))->get();
		}

		if(empty($start) and !empty($end))
		{
			return VisitDestinations::with($relations)
								 ->where($this->column, '<=', $dateFilters['end'])->get();
		}

		if(!empty($start) and empty($end))
		{
			return VisitDestinations::with($relations)
								 ->where($this->column, '>=', $dateFilters['start'])->get();
		}

		return $this->all();
	}

	/*
	* Get the investgations between two dates given a filter variable
	*/
	public function getFilteredClients($requestFilters)
	{
        $dateFilters = $requestFilters['date'];
        
		$dateFilters = $this->getDates($dateFilters);

        $filteredByDate = $this->getFilteredByDate($dateFilters);
        
        return $this-> getTotalGrouped($filteredByDate);
	} 

	/*
	* Group the investigations by a certain column
	*/
	public function getTotalGrouped($filteredByDate)
	{
        return $filteredByDate->groupBy('department')->reject(function($item, $index){

            return in_array($index, [
                'Inpatient.procedure-doctor', 'Inpatient.procedure-nurse', '0', 'Nurse', 'Ultrasound', 'Diagnostics'
            ]);

        });
	}

}