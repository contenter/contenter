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

		$autoloader->autoload('FinalView_Config');

		return $autoloader;
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
            Zend_Registry::set('Zend_Locale', $locale);
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
    * Add zend filters in filter path (zend doesn't make it by default)
    *
    */
    protected function _initZendFilters()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->addFilterPath('Zend/Filter', 'Zend_Filter');
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
		$locale = Zend_Registry::isRegistered('Zend_Locale') ? Zend_Registry::get('Zend_Locale') : null;
        $translator = new Zend_Translate('Gettext', APPLICATION_PATH . '/lang', $locale);
        Zend_Registry::set('Zend_Translate', $translator);

        // init view helper "Translate" to get static instance for short alias __()
        $this->bootstrap('ViewHelpers');
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

        if (!is_null($doctrine_config = $this->getOption('doctrine'))) {
            FinalView_Doctrine::init($doctrine_config);
        }
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
        if (filesize($file) > 0) {
            $routes = FinalView_Config::factory($file);
            Zend_Controller_Front::getInstance()->getRouter()->addConfig($routes, 'routes');
        }
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

        $view->navigation()->setAcl(FinalView_Acl::getInstance())->setUseAcl(false);

        $this->bootstrap('Translator');
        $view->navigation()->setTranslator(Zend_Registry::get('Zend_Translate'))->setUseTranslator(true);
    }

    /**
    * Assign navigation
    *
    * @param string $file
    * @param Zend_View $view
    */
    static public function assignNavigation($file, $view)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if (!in_array($ext, array('xml', 'yml'))) return;
        if (!isset($view->navigation)) {
            $view->navigation = array();
        }

        switch ($ext) {
            case 'xml':
                $navigation = new Zend_Config_Xml($file);
                $view->navigation[pathinfo($file, PATHINFO_FILENAME)] =
                    new Zend_Navigation($navigation->pages->toArray());
            break;
            case 'yml':
                $navigation = Doctrine_Parser::load($file, 'yml');
                $view->navigation[pathinfo($file, PATHINFO_FILENAME)] =
                    new FinalView_Navigation($navigation['Pages']);
            break;
        }
    }

    /**
     * Initializes ZFDebug console if application environment isn't production
     */
    protected function _initZFDebug()
    {
        if ($this->getOption('zf_debug')) {
            // Setup autoloader with namespace
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            // Ensure the front controller is initialized
            $this->bootstrap('FrontController');

            // Retrieve the front controller from the bootstrap registry
            $front = $this->getResource('FrontController');

            $options = array(
                'plugins' => array(
                    'Variables',
                    'FinalView_Controller_Plugin_Debug_Plugin_Doctrine',
                    'File' => array('base_path' => APPLICATION_PATH . '/../'),
                    'Memory',
                    'Time',
                    'Registry',
                    'Exception',
                    'Html',
                ),
                'jquery_path' => '/scripts/jquery.js'
            );

            $debug = new ZFDebug_Controller_Plugin_Debug($options);
            $front->registerPlugin($debug);

            return $debug;
        }

        return false;
    }
}
