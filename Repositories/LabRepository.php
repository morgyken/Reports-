<?php
namespace Ignite\Reports\Repositories;

use Ignite\Evaluation\Entities\Investigations;
use Ignite\Reports\Library\DateFilterTrait;
use Ignite\Reports\Library\ExcelReportTrait;
use Excel;

/*
Lab repository that sits between eloquent and the LabController
*/
class LabRepository
{
	use DateFilterTrait;

	/*
	* Define the investigation type for the laboratory procedures 
	*/
	protected $investigationType = 'laboratory';

	/*
	* Get all the investigations in the table
	*/
	public function all()
	{
		return Investigations::with(['procedures', 'visits'])->get();
	}	

	/*
	* Filter the investigations between two given dates
	*/
	public function getFilteredByDate($dateFilters)
	{
		$start = trim($dateFilters['start']);
		$end = trim($dateFilters['end']);
		$relations = ['procedures', 'visits.patients'];

		if(!empty($start) and !empty($end))
		{
			return Investigations::with($relations)
								 ->whereBetween($this->column, array_values($dateFilters))->get();
		}

		if(empty($start) and !empty($end))
		{
			return Investigations::with($relations)
								 ->where($this->column, '<=', $dateFilters['end'])->get();
		}

		if(!empty($start) and empty($end))
		{
			return Investigations::with($relations)
								 ->where($this->column, '>=', $dateFilters['start'])->get();
		}

		return $this->all();
	}

	/*
	* Get the investgations between two dates given a filter variable
	*/
	public function getFilteredInvestigations($requestFilters)
	{
		$dateFilters = $requestFilters['date'];

		$dateFilters = $this->getDates($dateFilters);

		$filteredByDate = $this->getFilteredByDate($dateFilters);

		return $this->getFilteredByAge($filteredByDate, $requestFilters['age']);
	}

	/*
	* Filters a collection by a certain age
	*/
	public function getFilteredByAge($collection, $filter)
	{
		if($filter != 'all')
		{
			$collection = $collection->filter(function($item) use($filter){

                if($item->visits->patients)
                {
                    $patientAge = $item->visits->patients->age;

                    return ($filter == '5') ? $patientAge <= 5 : $patientAge > 5;
                }
                
			});
		}

		return $collection;
	} 

	/*
	* Group the investigations by a certain column
	*/
	public function getTotalGrouped($investigations)
	{
		$investigations = $investigations->filter(function($investigation){

			return $investigation->type == 'laboratory';

		})->pluck('procedures')->groupBy('name');

		return $investigations->map(function($group){

			return $group->count();

		})->toArray();
	}

	/*
	* Generates a lab report and downloads it to an excel
	*/
	public function generateLabsReport($investigations)
	{
		ob_clean();

		Excel::create('lab_reports', function($excel) use($investigations){

		    $excel->sheet('laboratory_reports', function($sheet) use($investigations){

		    	$sheet->row(1, ['Procedure Done', 'Total']);

		    	$sheet->freezeFirstRow();

		    	foreach($investigations as $name => $quantity)
		    	{
		    		$sheet->appendRow([$name, $quantity]);
		    	}

		    });

		})->export('xls');
	}
}