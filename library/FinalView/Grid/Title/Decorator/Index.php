<?php
class FinalView_Grid_Title_Decorator_Index extends FinalView_Grid_Title_Decorator_Abstract
{
    public function render()
    {
        echo '<th class="fv_index">'.$this->_title.'</th>';
    }
}
