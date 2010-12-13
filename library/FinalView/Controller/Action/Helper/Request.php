<?php

class FinalView_Controller_Action_Helper_Request
    extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * Request params
     * 
     * @var array
     */
    private $_params = array();

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init()
    {
        $this->_initRequestParams();
        $this->_transformRequestParams();
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getParam($name, $default = null)
    {
        return $this->hasParam($name) ? $this->_params[$name] : $default;
    }

    public function hasParam($name)
    {
        return array_key_exists($name, $this->_params);
    }

    /**
     * Set params types if annotated
     * 
     */
    private function _initRequestParams()
    {
        $params = $this->getRequest()->getParams();
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        $this->_params = $params;
    }

    private function _transformRequestParams()
    {
        $actionClass = $this->getActionController();
        $actionMethod = $this->getFrontController()->getDispatcher()->getActionMethod($this->getRequest());
        $reflMethod = new Zend_Reflection_Method($actionClass, $actionMethod);
        try {
            $docblock = $reflMethod->getDocblock();
            $tagParams = $docblock->getTags();
            foreach ($this->_params as $paramName => &$paramValue) {

                foreach ($tagParams as $tagParam) {
                    /* @var $tagParam Zend_Reflection_Docblock_Tag_Param */

                    // work only with "param" tag
                    if ('param' != $tagParam->getName()) {
                        continue;
                    }

                    if ($this->_getRefParamName($tagParam) == $paramName) {
                        $this->_setParamType($paramValue, $this->_getRefParamTypes($tagParam));
                        break;
                    }
                }

            }
        } catch(Zend_Reflection_Exception $exception) {
            // no docblock annotation - no params transformation :)
        }
    }

    private function _setParamType(&$paramValue, array $refParamTypes)
    {
        // value IS NULL and can be NULL so everything ok
        if (is_null($paramValue) && $this->_paramCanBeNull($refParamTypes)) {
            return;
        }

        // search for the first NOT NULL type
        while ($type = array_shift($refParamTypes)) {
            if (!$this->_paramTypeIsNull($type)) {
                break;
            }
        }

        $basicTypes = array
        (
            'bool', 'boolean',
            'int', 'integer',
            'double', 'float',
            'string', 
            'array',
            'object',
        );

        switch(true)
        {
            case in_array($type, $basicTypes) :
                settype($paramValue, $type);
                break;
            case !$this->_paramTypeIsNull($type) && class_exists($type, true) :
                $paramValue = new $type($paramValue);
                break;
            default :
                trigger_error(sprintf('Invalid type %s given', $type), E_USER_ERROR);
                break;
        }
    }

    private function _getRefParamName(Zend_Reflection_Docblock_Tag_Param $tagParam)
    {
        return ltrim($tagParam->getVariableName(), '$');
    }

    private function _getRefParamTypes(Zend_Reflection_Docblock_Tag_Param $tagParam)
    {
        return array_filter(explode('|', $tagParam->getType()));
    }

    private function _paramCanBeNull(array $refParamTypes)
    {
        foreach ($refParamTypes as $type) {
            if ($this->_paramTypeIsNull($type)) {
                return true;
            }
        }

        return false;
    }

    private function _paramTypeIsNull($type)
    {
        return 0 === strcasecmp($type, 'null');
    }

}