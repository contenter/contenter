<?php
class FinalView_Grid_Renderer extends Zend_View
{    
    private $_scriptToRender;
    
    private $_namespace;
    
    public $grid;
    
    private $data = array();   
    
    public function __construct(FinalView_Grid $grid)
    {
        $this->grid = $grid;

        $this->addScriptPath(dirname(__FILE__).'/scripts');
        
        foreach ($this->getPlugins() as $plugin) {
        	$this->addScriptPath($plugin->getScriptsPath());
        }
        $this->addHelperPath('FinalView/View/Helper', 'FinalView_View_Helper');
    }
    
    public function getPlugins()
    {
        return $this->grid->getPlugins();
    }
    
    public function getPlugin($name)
    {
        return $this->grid->getPlugin($name);
    }
    
    public function getScript()
    {
        return $this->_scriptToRender;
    }
    
    public function setScript($script)
    {
        $this->_scriptToRender = $script;
    }
    
    public function clearScript()
    {
        $this->_scriptToRender = null;
    }    
    
    public function useNamespace($name)
    {
        $this->_namespace = $name;
        $this->data[$name] = array();
        return $this;
    }
    
    public function __set($name, $value)
    {        
        $namespace = $this->_namespace;
        $this->data[$namespace][$name] = $value;        
    }
    
    public function __get($name)
    {        
        $namespace = $this->_namespace;
        
        if (isset($this->data[$namespace][$name])) {
        	return $this->data[$namespace][$name];
        }
    }
    
    public function renderScript()
    {
        return parent::render($this->getScript() );
    }    
}