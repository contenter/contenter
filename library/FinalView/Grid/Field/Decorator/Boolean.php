<?php
class FinalView_Grid_Field_Decorator_Boolean extends FinalView_Grid_Field_Decorator_Abstract
{
    public function render($row, $fieldName)
    {
        if ($row[$fieldName]) {
            echo '<td>Yes</td>';
        } else {
            echo '<td>No</td>';
        }
    }
}