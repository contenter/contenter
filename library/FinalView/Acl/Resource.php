<?php

class FinalView_Acl_Resource implements Zend_Acl_Resource_Interface 
{
    
    /**
     * Unique id of Resource
     *
     * @var string
     */
    private $_resource_id;
    
    /**
    * Sets the Resource identifier
    * 
    * @param string $module
    * @param string $controller
    * @param string $action
    */
    public function __construct($module, $controller, $action)
    {
        $this->_resource_id = sprintf('%s:%s:%s', $module, $controller, $action);
    }

    /**
     * Defined by Zend_Acl_Resource_Interface; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resource_id;
    }
    
}