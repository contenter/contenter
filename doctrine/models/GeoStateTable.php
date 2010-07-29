<?php

class GeoStateTable extends Doctrine_Table
{

	public function getStates()
	{
		$query = $this->createQuery()
			->orderBy('name ASC')
			;

		return $query->execute();
	}

	public function getStatesAsOptions()
	{
		return $this->getStates()->toKeyValueArray('code', 'name');
	}

}