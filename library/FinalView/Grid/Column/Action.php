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
        
        $url_params = array();
        foreach ($params as $key => $value) {
            if (in_array($key, $this->iteratorFields)) {
                $url_params[$key] = $value;
            }         
        }        
        
        $view->url_params = $url_params;
        $view->url = $this->url;
        $view->label = $this->label;
    }
}
