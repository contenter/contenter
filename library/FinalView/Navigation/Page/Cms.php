<?php
class FinalView_Navigation_Page_Cms extends FinalView_Navigation_Page
{
    protected $_name;

    public function setPageName($name)
    {
        $this->_name = $name;
        $this->setRoute($name);
        $this->setParams(array('page_name' => $name));
    }

    public function getPageName()
    {
        return $this->_name;
    }

    public function setRoute($route)
    {
        if (null !== $route && (!is_string($route) || strlen($route) < 1)) {
            require_once 'Zend/Navigation/Exception.php';
            throw new Zend_Navigation_Exception(
                 'Invalid argument: $route must be a non-empty string or null');
        }

        $this->_route = $route;
        $this->_hrefCache = null;

        $this->setModule('Cms');
        $this->setController('index');
        $this->setAction('index');

        return $this;
    }
}
