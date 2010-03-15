<?php
interface FinalView_Grid_Action_Decorator_Interface
{
    public function preRender(); 
    public function render($row);
    public function postRender();
}
