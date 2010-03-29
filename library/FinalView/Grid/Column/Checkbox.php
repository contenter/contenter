<?php
class FinalView_Grid_Column_Checkbox extends FinalView_Grid_Column
{
    
    public function __construct($name, $iteratorField)
    {        
        parent::__construct($name, 'checkbox.phtml');
        
        $this->iteratorField = $iteratorField;                
    }
    
    public function handler(array $params, FinalView_Grid_Renderer $view)
    {
        $view->columnName = $this->getName();
        $view->value = $params[$this->iteratorField];
    }
}