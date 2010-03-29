<?php
class FinalView_Grid_Column_Title_Standard extends FinalView_Grid_Entity_Abstract
{
    public $_column;

    public function __construct(FinalView_Grid_Column $column)
    {
        $this->_column = $column;
        
        $this->setName($column->getName().'Title');
        
        $this->_script = 'column/title/'.basename($this->_column->getScript());
    }
    
    public function getColumn()
    {
        return $this->_column;
    }
    
    public function handler(array $params, FinalView_Grid_Renderer $view)
    {
        $view->title = $this->getColumn()->getName();
        $view->column = $this->getColumn();
    }
    
    
}