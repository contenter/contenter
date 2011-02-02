<?php
abstract class FinalView_Grid_Entity_Abstract
{
    private $_name;

    protected $_script;

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        if (!preg_match('/^[a-zA-Z]{1,1}[a-zA-Z0-9_-]*$/', $name)) {
            throw new FinalView_Grid_Exception($name.' must be /^[a-zA-Z]{1,1}[a-zA-Z0-9_-]*$/');
        }

        $this->_name = $name;
    }

    public function getScript()
    {
        return $this->_script;
    }

    public function setScript($script)
    {
        $this->_script = $script;
    }

    public function handler($params, FinalView_Grid_Renderer $view)
    {
        if (is_object($params)) {
            $view->value = $params;
        }elseif(is_array($params) || is_string($params)){
            $view->assign($params);
        }
    }
}
