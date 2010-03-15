<?php

class FinalView_Form_Element_CcExpDate extends Zend_Form_Element_Xhtml
{
    
    /**
     * Use formCcExpDate view helper 
     * @var string
     */
    public $helper = 'formCcExpDate';
    
    /**
     * Validate element value
     *
     * @param  mixed $value
     * @param  mixed $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $name = $this->getName();
        
        $value = sprintf
        (
            '%02d-%02d', 
            $context[$name]['month'], 
            $context[$name]['year']
        );
        
        $this->_value = $value;
        
        return parent::isValid($value, $context);
    }
    
}