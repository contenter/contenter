<?php
class FinalView_Grid_Field_Decorator_Checkbox extends FinalView_Grid_Field_Decorator_Abstract
{
    public function render($row, $fieldName)
    {
        echo '<td><input type="checkbox" name="'.$fieldName.'[]" value="'.$this->getValue($row, $fieldName).'" class="fv_fieldcheckbox" /></td>'.PHP_EOL;
    }
    
/*


<?php
class FinalView_Grid_Field_Decorator_Checkbox extends FinalView_Grid_Field_Decorator_Abstract
{
    protected $_label = '';
    
    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->_label;
    }
    
    public function render($row, $fieldName)
    {
        if ($this->getLoopField()) {
            echo '<td><label><input type="checkbox" name="'.$fieldName.'[]" value="'.
                    $row[$this->getLoopField()].'" class="fv_fieldcheckbox" /> '.$this->getLabel().
                 '</label></td>'.PHP_EOL;
        } else {
            echo '<td><input type="checkbox" name="'.$fieldName.'[]" value="'.
                    $row[$fieldName].'" class="fv_fieldcheckbox" /> '.$this->getLabel().
                 '</label></td>'.PHP_EOL;
        }
        
    }
}

 


 */    
}
