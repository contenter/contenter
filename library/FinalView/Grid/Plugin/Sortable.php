<?php
class FinalView_Grid_Plugin_Sortable extends FinalView_Grid_Plugin_Abstract
{    
    public $name = 'sortable';
    
    public $columnName;
    public $direction;
    
    public function __construct(array $sortableColumns)
    {
        $this->_columns = $sortableColumns;
    }
    
    public function init()
    {
        foreach ($this->_columns as $columnName) {
        	$this->_grid->getColumns()->$columnName->getTitle()->setScript('column/title/sortable.phtml');
        }
        
        $columnName = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort', null);
        
        if ($columnName) {
        	$this->columnName = $columnName;
            $direction = Zend_Controller_Front::getInstance()->getRequest()->getParam('direction', null);
        	
        	$this->direction = $direction == 'asc' ? 'desc' : 'asc';
        }        
    }
    
    public function getScriptsPath()
    {
        return dirname(__FILE__).'/scripts/Sortable';
    }
}