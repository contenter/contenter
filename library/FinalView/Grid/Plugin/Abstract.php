<?php
abstract class FinalView_Grid_Plugin_Abstract
{    
    protected $_grid;
    
    abstract public function getScriptsPath();
    
    public function setGrid($grid)
    {
        $this->_grid = $grid;
    }
}