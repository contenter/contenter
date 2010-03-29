<?php
abstract class FinalView_Grid_Entity_Abstract
{
    private $_name;
    
    protected $_script;
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function setName($name)
    {
        if (!preg_match('/^[a-zA-Z]{1,1}[a-zA-Z0-9]*$/', $name)) {
            throw new FinalView_Grid_Exception($name.' must be /^[a-zA-Z]{1,1}[a-zA-Z0-9]*$/');   	
        }
        
        $this->_name = $name;
    }
    
    public function getScript()
    {
        return $this->_script;
    }
    
    public function setScript($script)
    {
        $this->_script = $script;
    }
    
    public function handler(array $params, FinalView_Grid_Renderer $view)
    {
        $view->assign($params);
    }
}
