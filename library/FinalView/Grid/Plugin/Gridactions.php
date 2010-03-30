<?php
class FinalView_Grid_Plugin_Gridactions extends FinalView_Grid_Plugin_Abstract
{    
    public $name = 'gridactions';
    
    private $_actions;
    
    public function __construct(array $buttons)
    {
        $this->_actions = $buttons;
    }
    
    public function init()
    {
       
    }
    
    public function getActions()
    {
        return $this->_actions;
    }
    
    public function getScriptsPath()
    {
        return dirname(__FILE__).'/scripts/Gridactions';
    }
}