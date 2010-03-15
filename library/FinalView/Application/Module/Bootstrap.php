<?php

class FinalView_Application_Module_Bootstrap 
    extends Zend_Application_Module_Bootstrap 
{
    
    /**
     * Constructor
     * 
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application 
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);
        
        $r    = new ReflectionClass($this);
        $path = $r->getFileName();
        
        $this->setResourceLoader(new FinalView_Application_Module_Autoloader(array(
                'namespace' => $this->getModuleName(),
                'basePath'  => dirname($path),
            )));
    }
    
}