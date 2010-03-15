<?php
abstract class FinalView_Grid_Button_Decorator_Abstract implements FinalView_Grid_Button_Decorator_Interface
{
    protected $_button = array();
    /**
     * @var FinalView_Grid
     */
    protected $_grid = null;
    
    public function __construct(array $button)
    {
        $this->_button = $button;
    }
    
    /**
     * Set grid
     * @param FinalView_Grid $grid
     */
    public function setGrid(FinalView_Grid $grid)
    {
        $this->_grid = $grid;
    }
    /**
     * Return Grid
     * @return FinalView_Grid
     */
    public function getGrid()
    {
        return $this->_grid;
    }
    
    public function render() {}
}