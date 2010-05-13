<?php
class FinalView_Grid_Column_Iterator extends FinalView_Grid_Column
{
    
    public function __construct($name)
    {        
        parent::__construct($name, 'iterator.phtml');                
    }
    
    public function handler($params, FinalView_Grid_Renderer $view)
    {
//         dump($params->toArray());
//         exit;
        $name = $this->getName();
        $view->value = $params[$name];
    }
}
