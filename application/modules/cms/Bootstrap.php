<?php

class Cms_Bootstrap extends FinalView_Application_Module_Bootstrap 
{

    protected function _initCmsRoutes()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        foreach (Doctrine::getTable('CmsPage')->findByParams(array('route_exists' => true)) as $page)
        {
            $route = new Zend_Controller_Router_Route_Static($page->route, array(
                'module'        =>  'cms',
                'controller'    =>  'index',
                'action'        =>  'index',
                'page_name'     =>  $page->name
            ) );
            
            $router->addRoute($page->name, $route);
        }        
    }
}
