<?php
class FinalView_Grid_Action_Decorator_Abstract implements FinalView_Grid_Action_Decorator_Interface
{
    protected $_action = null;
    
    public function __construct(array $action)
    {
        $this->_action = $action;
    }
    
    public function render($row) {}
    public function preRender() {} 
    public function postRender() {}
}
