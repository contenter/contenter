<?php

/**
* This hepler does the same as Zend_View_Helper_Action expect it does not render 
* template vars and save them in viewRenderer object 
* 
*/
class FinalView_View_Helper_ActionVars extends Zend_View_Helper_Action 
{
    
    /**
    * Shortcut for action()
    * 
    * @param string $action
    * @param string $controller
    * @param string $module Defaults to default module
    * @param array $params
    */
    public function actionVars($action, $controller, $module = null, array $params = array()) 
    {
        $this->action($action, $controller, $module, $params);
    }
    
    /**
     * Retrieve rendered contents of a controller action
     *
     * If the action results in a forward or redirect, returns empty string.
     * 
     * @param  string $action 
     * @param  string $controller 
     * @param  string $module Defaults to default module
     * @param  array $params 
     */
    public function action($action, $controller, $module = null, array $params = array())
    {
        $this->resetObjects(); 
        if (null === $module) { 
            $module = $this->defaultModule; 
        } 

        // clone the view object to prevent over-writing of view variables
        $viewRendererObj = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        Zend_Controller_Action_HelperBroker::addHelper($viewRendererObj); 
        
        $this->request->setParams($params) 
                      ->setModuleName($module) 
                      ->setControllerName($controller) 
                      ->setActionName($action) 
                      ->setDispatched(true); 
 
        $this->dispatcher->dispatch($this->request, $this->response); 
    }
    
}