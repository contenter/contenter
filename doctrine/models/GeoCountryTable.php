<?php

class GeoCountryTable extends Doctrine_Table
{

	public function getCountries()
	{
		$query = $this->createQuery()
			->orderBy('code <> "US", name ASC')
			;

		return $query->execute();
	}

	public function getCountriesAsOptions()
	{
		return $this->getCountries()->toKeyValueArray('code', 'name');
	}

}