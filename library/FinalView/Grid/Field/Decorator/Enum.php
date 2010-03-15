<?php
class FinalView_Grid_Field_Decorator_Enum extends FinalView_Grid_Field_Decorator_Abstract
{
        
    public function __construct($labels = array())
    {
        $this->_labels = $labels;
    }
    
    public function render($row, $fieldName)
    {
        $value = $this->getValue($row, $fieldName);       
        echo '<td><span>'.(array_key_exists($value, $this->_labels)? $this->_labels[$value]:$value).'</span></label></td>'.PHP_EOL;
    }
}
