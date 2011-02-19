<?php

class FinalView_Acl_Resource implements Zend_Acl_Resource_Interface
{

    /**
     * Unique id of Resource
     *
     * @var string
     */
    private $_resource_id;

    private $_params = array();

    private $_resourceName = array();

    /**
    * Sets the Resource identifier
    *
    * @param string $module
    * @param string $controller
    * @param string $action
    */
    public function __construct($resource, array $params = array())
    {
        $this->_params = $params;
        $this->_resourceName = $resource;
        $this->_resource_id = $resource.'_'.base64_encode(serialize($params));
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

    public function getParams()
    {
        return $this->_params;
    }

    public function getResourceName()
    {
        return $this->_resourceName;
    }

}
