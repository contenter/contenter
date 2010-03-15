<?php
class FinalView_Grid_Title_Decorator_Standart extends FinalView_Grid_Title_Decorator_Abstract
{
    public function render()
    {
        echo '<th class="fv_actiontitle">'.$this->_title.'</th>';
    }
}
