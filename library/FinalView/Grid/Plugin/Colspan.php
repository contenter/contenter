<?php
class FinalView_Grid_Plugin_Colspan extends FinalView_Grid_Plugin_Abstract
{    
    public $name = 'colspan';
    
    public $jointColumns;
    private $isInJointColumn = false;
    private $restColumnsToJoin;
    
    public function __construct(array $jointColumns)
    {
        $this->jointColumns = $jointColumns;
    }
    
    public function init()
    {
        
    }
    
    public function renderColspan($column)
    {
        $countJointColumns = $this->jointColumns[$column];
        $this->isInJointColumn = true;
        $this->restColumnsToJoin = $this->jointColumns[$column];
        return ' colspan="'.$countJointColumns.'" ';        
    }
    
    public function isInJointColumn()
    {
        return $this->isInJointColumn;
    }
    
    public function decJointColumn()
    {
        $this->restColumnsToJoin--;
        if ($this->restColumnsToJoin == 0) {
            $this->isInJointColumn = false;
        }
    }
    
    public function getScriptsPath()
    {
        return dirname(__FILE__).'/scripts/Colspan';
    }
}