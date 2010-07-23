<?php
class FinalView_Navigation extends Zend_Navigation
{
    protected $_params = array();
    
    
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }
    
    public function getParams()
    {
        return $this->_params;
    }
}
