<?php
class FinalView_Grid_Field_Decorator_Boolcheckbox extends FinalView_Grid_Field_Decorator_Abstract
{
    
    protected $_labelYes = '';
    protected $_labelNo = '';
    
    public function setLabelYes($label)
    {
        $this->_labelYes = $label;
        return $this;
    }

    public function setLabelNo($label) 
    {
        $this->_labelNo = $label;
        return $this;
    }
    
    public function render($row, $fieldName)
    {
        echo '<td><label><input type="checkbox" name="'.$fieldName.'[]" value="'.
                $row[$this->getLoopField() ? $this->getLoopField() : $fieldName].'" class="fv_boolcheckbox" '.
                (  $row[$fieldName] ? 'checked="checked"' : '' ).
                ' /> <span>'.(  $row[$fieldName] ? $this->_labelYes : $this->_labelNo ).'</span></label></td>'.PHP_EOL;
    }
}
