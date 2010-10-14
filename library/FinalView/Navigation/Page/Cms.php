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
        parent::setRoute($route);

        $this->setModule('Cms');
        $this->setController('index');
        $this->setAction('index');

        return $this;
    }
}
