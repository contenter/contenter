<?php

class MailTemplatesTable extends FinalView_Doctrine_Table
{
    
    protected function templateSelector($template)
    {
        $this->_getQuery()->addWhere($this->getTableName() . '.template = ?', $template);
    }    
}
