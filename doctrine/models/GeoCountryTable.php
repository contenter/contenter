<?php

class GeoCountryTable extends FinalView_Doctrine_Table
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

        public function getCountriesAsOptionsId()
	{
		return $this->getCountries()->toKeyValueArray('id', 'name');
	}

         protected function innerJoinCountrySelector($params)
         {
            $this->innerJoin('Country', $params);
         }
}