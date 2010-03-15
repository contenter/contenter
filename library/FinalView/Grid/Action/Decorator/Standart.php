<?php
class FinalView_Grid_Action_Decorator_Standart extends FinalView_Grid_Action_Decorator_Abstract
{
    public function preRender()
    {
        echo '<td class="fv_tdaction">';
    }
    public function render($row)
    {
        echo '<a href="/'.implode('/', $this->_action['uri']).'/'.$this->_action['field'].'/'.$row[$this->_action['field']].'">';
        echo $this->_action['title'];
        echo '</a>';
    }
    
    public function postRender()
    {
        echo '</td>';
    }
}
