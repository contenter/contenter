<?php
/**
* Exclude magic_quotes_gpc influence. Implemented on the application level
* as PHP can be started in CGI mode.
*
*/
if (get_magic_quotes_gpc()) {
    array_apply_recursive($_GET, 'stripslashes');
    array_apply_recursive($_POST, 'stripslashes');
    array_apply_recursive($_COOKIE, 'stripslashes');
    array_apply_recursive($_REQUEST, 'stripslashes');
}

class FinalView_Bootstrap extends Zend_Application_Bootstrap_Bootstrap 
{
    
    protected function _initFinalViewNamespace()
    {
        $autoloader = $this->getApplication()->getAutoloader();
        $autoloader->registerNamespace('FinalView');    
    }
    
    protected function _initAplicationAutoloader()
    {        
        $this->bootstrap('FinalViewNamespace');
        
        $autoloaderAppNamespace = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'Application_',
            'basePath'  => APPLICATION_PATH,
        ));
        
        $autoloaderAppNamespace->addResourceType('plugins', '/plugins', 'Plugin');        
        
        return $autoloaderAppNamespace;
    }
    
    protected function _initTimezone() 
    {
        if ($timezone = $this->getOption('timezone')) {
            ini_set('date.timezone', 'UTC');
        }
    }
    
	protected function _initLocale()
	{
		if ($locale = $this->getOption('locale')) {
            Zend_Registry::set('locale', $locale);
        }
	}
    
    protected function _initMagicFile() 
    {
        if ($magicfile = $this->getOption('magicfile')) {
            defined('MAGIC') or define('MAGIC', $magicfile);
        }
    }
    
    /**
    * Add view helpers path
    * 
    */
    protected function _initViewHelpers() 
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        $view->addHelperPath('FinalView/View/Helper', 'FinalView_View_Helper');
    }
    
    /**
    * Register finalview action helpers
    * 
    */
    protected function _initControllerHelpers() 
    {
        $this->bootstrap('AplicationAutoloader');
        
        Zend_Controller_Action_HelperBroker::
            addPrefix('FinalView_Controller_Action_Helper');
    }
    
    /**
    * Init Translator
    * 
    */
    protected function _initTranslator()
    {
        $translator = new Zend_Translate('Gettext', APPLICATION_PATH . '/lang');
        Zend_Registry::set('Zend_Translate', $translator);
        
        // init view helper "Translate" to get static instance for short alias __()
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->getHelper('Translate');
    }
    
    protected function _initDateFormat() 
    {
        $this->bootstrap('view');
        
        $view = $this->getResource('view');
        $translator = $view->getHelper('Translate');
        
        $this->bootstrap('AplicationAutoloader');
        FinalView_View_Helper_DateFormat::setFormat($translator->translate('DATE_FORMAT'));
    }
    
    /**
    * Register current module plugin
    * 
    * In each class Bootstrap of modules may be defined a public method init (), 
    * which is responsible for initializing the module if it is current. Also, 
    * for each module can be defined translation files that are placed in the folder lang of module.
    * This logic is described in the plugin FinalView_Controller_Plugin_InitApplication            
    */
    protected function _initApplication() 
    {
        $this->bootstrap('AplicationAutoloader');
        
        $this->bootstrap('FrontController');
        
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new FinalView_Controller_Plugin_InitApplication);
    }
    
    /**
    * Init Doctrine
    * 
    */
    protected function _initDoctrine()
    {        
        $this->bootstrap('AplicationAutoloader');
        require_once 'Doctrine.php';
        
        $this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'autoload'));
        $this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'modelsAutoload'));
        $this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'extensionsAutoload'));
        
        $manager = Doctrine_Manager::getInstance();
        
        if (is_null($this->getOption('doctrine'))) return $manager;
        
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        $manager->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, 
            Doctrine::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        
        // Add models and generated base classes to Doctrine autoloader
        $doctrineConfig = $this->getOption('doctrine');
        
        Doctrine::loadModels($doctrineConfig['models_path']);
        
        $manager->openConnection($doctrineConfig['connection_string']);
        Doctrine_Manager::connection()->setCharset('UTF8');
        
        Doctrine_Manager::getInstance()->registerHydrator(
            'SimpleScalarHydrator','FinalView_Doctrine_Hydrator_SimpleScalarDriver');
        
        return $manager;
    }
    
    /**
    * Init routes
    * 
    */
    protected function _initRouter() 
    {       
        $this->bootstrap('FrontController');
        
        Zend_Controller_Front::getInstance()->getRouter()->removeDefaultRoutes();             
        
        // add all found routes 
        iterate_resursive(APPLICATION_PATH . '/routes/', 
            array(__CLASS__, 'addRoutes'));        
    }
    
    /**
    * Add routes to the router
    * 
    * @param string $file
    */
    static public function addRoutes($file) 
    {
        if (pathinfo($file, PATHINFO_EXTENSION) != 'xml') return;
        $routes = new Zend_Config_Xml($file);
        Zend_Controller_Front::getInstance()->getRouter()->addConfig($routes, 'routes'); 
    }
    
    /**
    * Init navigation
    * 
    */
    protected function _initNavigation()
    {
        // bootstrap view
        $this->bootstrap('view');
        
        $view = $this->getResource('view');
        
        // assign all found navigations 
        iterate_resursive(APPLICATION_PATH . '/navigation/', 
            array(__CLASS__, 'assignNavigation'), $view);
    }
    
    /**
    * Assign navigation
    * 
    * @param string $file
    * @param Zend_View $view
    */
    static public function assignNavigation($file, $view) 
    {
        if (pathinfo($file, PATHINFO_EXTENSION) != 'xml') return;
        if (!isset($view->navigation)) {
            $view->navigation = array();
        }
        
        $navigation = new Zend_Config_Xml($file);
        $view->navigation[pathinfo($file, PATHINFO_FILENAME)] = 
            new Zend_Navigation($navigation->pages->toArray());
    }
    
}
