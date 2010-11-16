<?php

/**
 * CmsTable
 */
class CmsPageTable extends FinalView_Doctrine_Table
{ 
    protected function pageNameSelector($name)
    {
        $this->_getQuery()->addWhere($this->getTableName().'.name = ?', $name);        
    }
    
    protected function routeExistsSelector($availability)
    {
        if ($availability) {
        	$this->_getQuery()->addWhere($this->getTableName().'.route is not null');
        }else{
            $this->_getQuery()->addWhere($this->getTableName().'.route is null');
        }
    }     
}
