<?php

/**
* Init current module 
* 
*/
class FinalView_Controller_Plugin_InitApplication 
    extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!defined('BASE_PATH')) {
            if ($request instanceof FinalView_Controller_Request_Cli) {
                $scheme = 'http';
                $http_host = $_SERVER['HTTP_HOST'] = 
                    Zend_Controller_Front::getInstance()
                    ->getParam('bootstrap')
                    ->getOption('http_host');
            } else {
                $scheme = $request->getScheme();
                $http_host = $request->getHttpHost();
            }
            
            define('BASE_PATH', $scheme . '://' . $http_host . '/');
        }
    }
    
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $current_module = $request->getModuleName();
        $modules = $bootstrap->getResource('modules');
        
        // custom current module init 
        if (array_key_exists($current_module, $modules) && 
            method_exists($modules[$current_module], 'init')) 
        {
            $modules[$current_module]->init();
        }
        
        // add current module translations
        if (is_dir($lang_path = APPLICATION_PATH . 
            sprintf('/modules/%s/lang', $current_module))) 
        {
            $locale = Zend_Registry::isRegistered('Zend_Locale') ? Zend_Registry::get('Zend_Locale') : null;
            Zend_Registry::get('Zend_Translate')->addTranslation($lang_path, $locale);
        }
    }
    
}