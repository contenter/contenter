<?php
abstract class FinalView_Grid_Field_Decorator_Abstract implements FinalView_Grid_Field_Decorator_Interface
{
    protected $_loopField;

    public function setLoopField($field)
    {
        $this->_loopField = $field;
        return $this;
    }
    
    public function getLoopField()
    {
        return $this->_loopField;
    }
    
    public function getValue($row, $fieldName)
    {
        if (count($complex_fields = explode('->', $fieldName)) > 1) {
            $result = $row;            
            while(($single_field = array_shift($complex_fields)) !== null) {
                $result = $result->get($single_field);
            }
        } else {
            $result = $row->$fieldName;
        }
        return $result;     
    }
    
    public function render($row, $fieldName) {}
    
}