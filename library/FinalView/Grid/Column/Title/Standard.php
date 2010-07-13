<?php
class FinalView_Grid_Column_Title_Standard extends FinalView_Grid_Entity_Abstract
{
    public $_column;
    
    protected $_label;

    public function __construct(FinalView_Grid_Column $column)
    {
        $this->_column = $column;
        
        $this->setName($column->getName().'Title');
        
        $this->_script = 'column/title/'.basename($this->_column->getScript());
        
        $this->_label = $column->getName();
    }
    
    public function getColumn()
    {
        return $this->_column;
    }
    
    public function handler($params, FinalView_Grid_Renderer $view)
    {
        $view->title = $this->getLabel();
        $view->column = $this->getColumn();
    }
    
    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->_label;
    }
}
