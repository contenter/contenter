<?php

class FinalView_View_Helper_HtmlTagAttribs extends Zend_View_Helper_HtmlElement
{
    
    /**
    * Generates an 'Image' element.
    * 
    * @param string $src
    * @param mixed $attribs
    * @param boolean $escape
    * @return string
    */
    public function htmlTagAttribs($attribs = array()) 
    {        
        return $this->_htmlAttribs($attribs);
    }
    
}