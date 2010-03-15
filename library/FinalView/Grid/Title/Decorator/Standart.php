<?php
class FinalView_Grid_Title_Decorator_Standart extends FinalView_Grid_Title_Decorator_Abstract
{
    public function render()
    {
        echo '<th>'.$this->_title.'</th>';
    }
}
