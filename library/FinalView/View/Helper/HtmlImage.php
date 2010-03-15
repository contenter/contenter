<?php

class FinalView_View_Helper_HtmlImage extends Zend_View_Helper_FormElement
{
    
    /**
    * Generates an 'Image' element.
    * 
    * @param string $src
    * @param mixed $attribs
    * @param boolean $escape
    * @return string
    */
    public function htmlImage($src, $attribs = false, $escape = true) 
    {
        if ($escape) {
            $src = $this->view->escape($src);
        }
        
        
        if (is_array($attribs)) {
            $exclude_attribs = array_filter($attribs, 
                create_function('$value', 'return is_null($value);'));
            $default_attribs = $this->_getDefaultAttribs($src, $exclude_attribs);
            $attribs = array_filter($attribs, 
                create_function('$value', 'return !is_null($value);'));
        } else {
            $attribs = $this->_getDefaultAttribs($src);
        }
        
        $attribs = $this->_htmlAttribs($attribs);
        
        return '<img src="' . $src . '" ' . $attribs . ' />';
    }
    
    /**
    * Set default attributes
    * 
    * @param string $src
    * @param array $exclude
    * @return array
    */
    private function _getDefaultAttribs($src, array $exclude = array()) 
    {
        if (file_exists($src) && is_file($src)) {
            list($width, $height, , ) = getimagesize($src);
        } else {
            $width = $height = '';
        }
        
        $default = array
        (
            'alt' => '', 
            'width' => $width, 
            'height' => $height, 
        );
        
        return array_diff_key($default, $exclude);
    }
    
}