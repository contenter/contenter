<?php
class FinalView_Grid_Renderer extends Zend_View
{
    private $_scriptToRender;

    private $_namespace;
    private $_view;

    public $grid;

    public $data = array();

    public function __construct(FinalView_Grid $grid)
    {
        $this->grid = $grid;
        $this->_view = Zend_Layout::getMvcInstance()->getView();
        $this->addScriptPath(dirname(__FILE__).'/scripts');
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
        if (!isset($this->data[$name])) $this->data[$name] = array();
        return $this;
    }

    public function currentNamespace()
    {
        return $this->_namespace;
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

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_view, $name), $args);
    }
}
