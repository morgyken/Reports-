<?php
namespace Ignite\Reports\Library;

trait DateFilterTrait
{
	protected $startDate, $endDate = null;

	protected $column = 'created_at';

	/**
	* Set the filters into a session variable
	*/
	public function setDateFilters()
	{
		session([
			'dateFilters' => [
				'start' => $this->startDate,
				'end' => $this->endDate
			]
		]);
	}

	/**
	* Get the filters from the session variable
	*/
	public function getDateFilters()
	{
		return session('dateFilters');
	}

	/**
	* Set the date that is required by passing a filter variable with the specific key
	*/
	public function setDates($dateFilters)
	{
		if(is_array($dateFilters))
		{
			$this->startDate = $this->processDate($dateFilters, 'start');

			$this->endDate = $this->processDate($dateFilters, 'end');
		}

		$this->setDateFilters();

		return $this;
	}

	/**
	* Return the dates 
	*/
	public function getDates($dateFilters)
	{
		$this->setDates($dateFilters);

		return [ 
			'start' => $this->startDate, 
			'end' => $this->endDate 
		];
	}

	/**
	* Set the column that should be queried in the database
	*/
	public function setColumn($column)
	{
		$this->column = $column;

		return $this;
	}

	/**
	* Checks to see if the filter contains the keys and returns an appropriate array
	*/
	public function processDate($filter, $key)
	{
		$clean = [];

		foreach($filter as $index => $value)
		{
			$clean[trim($index, "'")] = $value;
		}

		return !isset($clean[$key]) ?: $clean[$key];
	}
}