<?php
namespace Ignite\Reports\Library;

trait ExcelReportTrait
{
	protected $heading;

	// protected $

	/*
	* Set the heading to the excel sheet
	*/
	public function setReportHeading($heading)
	{
		$this->heading = $heading;
	}

	/*
	* Generate the report 
	*/
	public function generate()
	{
		Excel::create('Lab Reports', function($excel) {

		    $excel->sheet('Sheetname', function($sheet) {

		        $sheet->fromArray(array(
		            array('data1', 'data2'),
		            array('data3', 'data4')
		        ));

		    });

		})->export('xls');
	}
}