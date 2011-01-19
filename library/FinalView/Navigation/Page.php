<?php
class FinalView_Navigation_Page extends Zend_Navigation_Page_Mvc
{

    protected $_params = null;
    protected $_activeForRoutes = array();

    public function isActive($recursive = false)
    {
        if (!$this->_active) {
            $front = Zend_Controller_Front::getInstance();

            $routerName = $front->getRouter()->getCurrentRouteName();

            if ($this->isActiveForRoute($routerName) ){
                return true;
            }

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
        $resource = $route->getDefault('module') . '.' . $route->getDefault('controller') . '.' . $route->getDefault('action');

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
        }

        return parent::getResource();
    }

    public function setResource($resource = null)
    {
        if (is_string($resource)) {
            return parent::setResource(new FinalView_Acl_Resource($resource, $this->getParams()));
        }

        return parent::setResource($resource);
    }

    public function getParams()
    {
        if (null === $this->_params) {
            if (is_null($this->getParent())) {
                $this->setParams(array());
            }else{
                $this->setParams($this->getParent()->getParams());
            }
        }
        return (array)parent::getParams();
    }

    public function setActiveForRoutes($routes)
    {
        $this->_activeForRoutes = $routes;
    }

    public function isActiveForRoute($route)
    {
        return in_array($route, $this->_activeForRoutes);
    }

    public function setRoute($route)
    {
        parent::setRoute($route);

        $pageRoute = Zend_Controller_Front::getInstance()->getRouter()->getRoute($this->_route);

        $this->setModule($pageRoute->getDefault('module'));
        $this->setController($pageRoute->getDefault('controller'));
        $this->setAction($pageRoute->getDefault('action'));

        return $this;
    }
}
