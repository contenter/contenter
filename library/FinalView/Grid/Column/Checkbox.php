<?php
class FinalView_Grid_Column_Checkbox extends FinalView_Grid_Column
{
    
    protected $iteratorField;
    
    public function __construct($name, $iteratorField = null)
    {        
        parent::__construct($name, 'checkbox.phtml');
        
        $this->iteratorField = $iteratorField;                
    }
    
    public function handler($params, FinalView_Grid_Renderer $view)
    {
        $view->value = ($this->iteratorField === null) ? '' : @$params[$this->iteratorField];
    }
}
