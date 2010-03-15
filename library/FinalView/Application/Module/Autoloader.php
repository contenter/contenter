<?php

class FinalView_Application_Module_Autoloader 
    extends Zend_Application_Module_Autoloader
{
    
    /**
     * Initialize default resource types for module resource classes
     * 
     * @return void
     */
    public function initDefaultResourceTypes()
    {
        parent::initDefaultResourceTypes();
        
        $this->addResourceTypes(array(
            'validator' => array(
                'namespace' => 'Validator',
                'path' => 'validators',
            ),
            'filter' => array(
                'namespace' => 'Filter',
                'path' => 'filters',
            ),
        ));
        
        $resources_path = APPLICATION_PATH . '/configs/module_bootstrap_resources.xml';
        if (file_exists($resources_path)) {
            $resources = new Zend_Config_Xml($resources_path);
            $this->addResourceTypes($resources->resources->toArray());
        }
    }
    
}