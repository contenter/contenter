<?php
class FinalView_Grid_Column_Action extends FinalView_Grid_Column
{
    
    protected $url;
    protected $iteratorFields;
    protected $label;
    
    public function __construct($name, $label, $url = null, $iteratorFields = array())
    {        
        parent::__construct($name, 'action.phtml');
        
        $this->url = $url;
        $this->iteratorFields = $iteratorFields;
        $this->label = $label;                
    }
    
    public function handler($params, FinalView_Grid_Renderer $view)
    {
        $view->columnName = $this->getName();
        $view->url_params = array_intersect_key($params, array_flip($this->iteratorFields) );
        $view->url = $this->url;
        $view->label = $this->label;
    }
}
