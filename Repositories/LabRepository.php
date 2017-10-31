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
		return Investigations::with(['procedures'])->get();
	}	

	/*
	* Filter the investigations between two given dates
	*/
	public function getFilteredByDate($dateFilters)
	{
		$start = $dateFilters['start'];
		$end = $dateFilters['end'];

		if(!is_null($start) and !is_null($end))
		{
			$range = array_values($dateFilters);

			return Investigations::with(['procedures'])
								 ->whereBetween($this->column, $range)->get();
		}

		if(is_null($start) and !is_null($end))
		{
			return Investigations::with(['procedures'])
								 ->where($this->column, '<=', $dateFilters['end'])->get();
		}

		if(!is_null($start) and is_null($end))
		{
			return Investigations::with(['procedures'])
								 ->where($this->column, '>=', $dateFilters['start'])->get();
		}

		return $this->all();
	}

	/*
	* Get the investgations between two dates given a filter variable
	*/
	public function getFilteredInvestigations($requestFilters)
	{
		$dateFilters = $this->getDates($requestFilters);

		return $this->getFilteredByDate($dateFilters);
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