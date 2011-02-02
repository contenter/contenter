<?php
class FinalView_Grid_Plugin_Sortable extends FinalView_Grid_Plugin_Abstract
{    
    public $name = 'sortable';
    
    public $columnName;
    public $direction;
    
    protected $_sortParams;
    protected $_defaultSortParams;

    protected $_columns = array();
    
    public function __construct(array $sortableColumns = null)
    {
        if(!is_null($sortableColumns)) {
            $this->setColumns($sortableColumns);
        }
    }
    
    public function init()
    {
        foreach ($this->_columns as $columnName) {
            $this->_grid->getColumns()->$columnName->getTitle()->setScript('column/title/sortable.phtml');
        }
    }
    
    public function getScriptsPath()
    {
        return dirname(__FILE__).'/scripts/Sortable';
    }
    
    public function setColumns(array $sortableColumns)
    {
        $this->_columns = $sortableColumns;
        return $this;
    }
    
    public function setSortParams(array $sortParams)
    {
        $this->_sortParams = new stdClass;
        $this->_sortParams->columnName = $sortParams['field'];
        $this->_sortParams->direction  = $sortParams['direction'] == 'asc' ? 'asc' : 'desc';
        
        return $this;
    }

    public function getSortParams()
    {
        if (is_null($this->_sortParams)) {
            $this->setSortParams(array(
                'field'         =>  null,
                'direction'     =>  null,
            ));
        }

        return $this->_sortParams;
    }
    
    public function setDefaultSortParams(array $sortParams)
    {
        $this->_defaultSortParams = $sortParams;
    }
}