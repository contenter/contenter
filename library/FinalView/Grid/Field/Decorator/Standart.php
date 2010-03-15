<?php
class FinalView_Grid_Field_Decorator_Standart 
    extends FinalView_Grid_Field_Decorator_Abstract
{
    
    private $_nl2br = false;
    
    public function __construct(array $options = array()) 
    {
        if (array_key_exists('nl2br', $options) && (bool)$options['nl2br']) {
            $this->_nl2br = true;
        }
    }
    
    public function render($row, $fieldName)
    {
        $value = $this->getValue($row, $fieldName);
        
        if ($this->_nl2br) {
            $value = nl2br($value);
        }
        echo sprintf('<td>%s</td>' . PHP_EOL, $value);
    }
    
}
