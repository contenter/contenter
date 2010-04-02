<?php

/**
 * UserTable
 */
class UserTable extends FinalView_Doctrine_Table
{
    protected function emailSelector($email)
    {
        $this->_getQuery()->addWhere($this->getTableName().'.email = ?', $email);        
    }
    
    protected function idSelector($id)
    {
        $this->_getQuery()->addWhere($this->getTableName().'.id = ?', $id);        
    }      
}