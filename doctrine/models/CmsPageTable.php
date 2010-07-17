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
}
