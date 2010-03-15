<?php

class FinalView_View_Helper_FormCommonErrors extends Zend_View_Helper_FormElement
{
    
    /**
     * @var Zend_Form_Element
     */
    protected $_element;

    /**#@+
     * @var string Element block start/end tags and separator
     */
    protected $_htmlElementEnd       = '</li></ul>';
    protected $_htmlElementStart     = '<ul%s><li>';
    protected $_htmlElementSeparator = '</li><li>';
    /**#@-*/

    
    /**
     * Render all form errors
     *
     * @param  string|array $errors Error(s) to render
     * @param  array $options
     * @return string
     */
    public function formCommonErrors(Zend_Form $form, array $options = null)
    {
        if (empty($options['class'])) {
            $options['class'] = 'error';
        }
        if (empty($options['id'])) {
            $options['id'] = 'message-container';
        }

        $start = $this->getElementStart();
        if (strstr($start, '%s')) {
            $attribs = $this->_htmlAttribs($options);
            $start   = sprintf($start, $attribs);
        }
        
        
        // gather form elements errors
        $html  = '';
        
        
        if (count($form->getErrorMessages())) {
            $errors = array();
            
            $html  = $start;
            $html .= $this->view->FormErrors($form->getErrorMessages(), array('class' => 'common_errors'));
            $html .= 
                implode($this->getElementSeparator(), (array) $errors)
               . $this->getElementEnd();
        }
        
        return $html;
    }
    
    /**
     * Set end string for displaying errors
     *
     * @param  string $string
     * @return Zend_View_Helper_FormErrors
     */
    public function setElementEnd($string)
    {
        $this->_htmlElementEnd = (string) $string;
        return $this;
    }

    /**
     * Retrieve end string for displaying errors
     *
     * @return string
     */
    public function getElementEnd()
    {
        return $this->_htmlElementEnd;
    }

    /**
     * Set separator string for displaying errors
     *
     * @param  string $string
     * @return Zend_View_Helper_FormErrors
     */
    public function setElementSeparator($string)
    {
        $this->_htmlElementSeparator = (string) $string;
        return $this;
    }

    /**
     * Retrieve separator string for displaying errors
     *
     * @return string
     */
    public function getElementSeparator()
    {
        return $this->_htmlElementSeparator;
    }

    /**
     * Set start string for displaying errors
     *
     * @param  string $string
     * @return Zend_View_Helper_FormErrors
     */
    public function setElementStart($string)
    {
        $this->_htmlElementStart = (string) $string;
        return $this;
    }

    /**
     * Retrieve start string for displaying errors
     *
     * @return string
     */
    public function getElementStart()
    {
        return $this->_htmlElementStart;
    }
    
}
