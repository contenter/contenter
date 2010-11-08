<?php

/**
* Php vars to JsVars
* 
*/
class FinalView_View_Helper_JData extends Zend_View_Helper_Abstract
{
    
    private $_data = array();
    private $_elements = array();

    private static function _getElementObject($element)
    {
        if (is_string($element)) {
            $elementObj = new Zend_Json_Expr(Zend_Json::encode($element));
        }elseif($element instanceof Zend_Json_Expr) {
            $elementObj = $element;
        }else{
            throw new FinalView_View_Exception('Element must be string or instamce of Zend_Json_Expr');
        }
        
        return $elementObj;
    }
    
    private static function _getElementHash(Zend_Json_Expr $element)
    {
        return md5($element->__toString());
    }

    public function jData($element = null, $key = null, $value = null)
    {
        switch (true) {
            case is_null($element):
                $this->setElement(null);
                return $this;
            break;
            case is_null($key):
                $this->setElement($element);
                return $this;
            break;
            case is_null($value):
                return $this->_getData($element, $key);
            break;
        }

        $this->_setData($element, $key, $value);
        return $this;
    }
    
    public function setElement($element)
    {
        if (is_null($element)) {
            $this->_element = null;
            return $this;
        }
        $this->_element = self::_getElementObject($element);
        return $this;
    }
    
    private function _getScriptForElement($element)
    {
        $hash = self::_getElementHash($element);
        
        if (!isset($this->_data[$hash])) {
            return '';
        }

        $js = array();
        foreach ($this->_data[$hash] as $key => $value) {
            $js[] = '$(' . $element->__toString() . ').data(\'' . $key . '\', ' . Zend_Json::encode($value) . ')';
        }
        
        $js_code = empty($js) ? '' : implode(';', $js) . ';';

        return $js_code;
    }
    
    public function getScript()
    {
        if (!is_null($this->_element)) {
            return $this->_getScriptForElement($this->_element);
        }
        
        $js = array();
        foreach ($this->_elements as $hash=>$obj) {
            $element_code = $this->_getScriptForElement($obj);
            if (!empty($element_code)) {
                $js[] = $element_code;
            }
        }
        
        $js_code = '';
        if (!empty($js)) {
            $js_code = '$(function(){' . implode('', $js) . '});';
        }
        
        return $js_code;
    }

    private function _setData($element, $key, $value)
    {
        $elementObj = self::_getElementObject($element);
        $hash = self::_getElementHash($elementObj);

        $this->_elements[$hash] = $elementObj;
        $this->_data[$hash][$key] = $value;
    }
    
    private function _getData($element, $key)
    {
        $elementObj = self::_getElementObject($element);
        $hash = self::_getElementHash($elementObj);

        return @$this->_data[$hash][$key];
    }
}
