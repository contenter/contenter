<?php
/**
 */
class FinalView_Form_Element_Date extends Zend_Form_Element_Xhtml

{
    /**
     * Use formDate view helper 
     * @var string
     */
    public $helper = 'formDate';
    
    /**
     * Validate element value
     *
     * @param  mixed $value
     * @param  mixed $context
     * @return boolean
     */
    public function isValid ($value, $context = null)
    {
        $name = $this->getName();

        if(array_key_exists($name, $context)) {
            $value = sprintf
            (
                '%04d-%02d-%02d', 
                $context[$name]['year'], 
                $context[$name]['month'],
                $context[$name]['day']
            );
                        
            $this->_value = $value;
        }
        
        return parent::isValid($value, $context);
    }
    
}
