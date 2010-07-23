<?php
class FinalView_Navigation_Page extends Zend_Navigation_Page_Mvc
{   
    
    protected $_params = null;
    
    public function isActive($recursive = false)
    {
        if (!$this->_active) {
            $front = Zend_Controller_Front::getInstance();
            
            $routerName = $front->getRouter()->getCurrentRouteName();
            
            if ($routerName != $this->getRoute()) {
            	return parent::isActive($recursive);
            }
            
            $params = array_intersect_assoc($this->getParams(), $front->getRequest()->getParams());
            
            if (count($params) == count($this->getParams()) ) {
                $this->_active = true;
                return true;
            }
        }

        return parent::isActive($recursive);
    }
    
    protected function _generateResource()
    {
    	$router = Zend_Controller_Front::getInstance()->getRouter();
    	$route = $router->getRoute($this->getRoute() );
    	$resource = $route->getDefault('module') . '-' . $route->getDefault('controller') . '-' . $route->getDefault('action');
        
        return new FinalView_Acl_Resource($resource, $this->getParams());        
    }
    
    public function attachAclResource()
    {
        $this->setResource($this->_generateResource());
    }
    
    public function getResource()
    {
        if (is_null($this->_resource)) {
        	$this->attachAclResource();
        	FinalView_Acl::getInstance()->addResource($this->_resource);
        }
        
        return parent::getResource();
    }
    
    public function getParams()
    {
        if (null === $this->_params) {
        	$this->setParams($this->getParent()->getParams());
        }
        return (array)parent::getParams();
    } 
}
