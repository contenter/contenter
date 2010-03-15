<?php
interface FinalView_Grid_Button_Decorator_Interface
{
    public function getGrid();
    public function setGrid(FinalView_Grid $grid);
    public function render();
}