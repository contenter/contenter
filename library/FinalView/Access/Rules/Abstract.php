<?php
class FinalView_Access_Rules_Abstract
{
    protected $_params = array();
    
    public function __construct(array $params)
    {
        $this->_params = $params;
    }
    
    public function isPostRule()
    {
        return Zend_Controller_Front::getInstance()->getRequest()->isPost();
    }
    
    public function isGetRule()
    {
        return Zend_Controller_Front::getInstance()->getRequest()->isGet();
    }
}
