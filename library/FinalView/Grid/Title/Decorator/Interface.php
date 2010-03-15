<?php
interface FinalView_Grid_Title_Decorator_Interface
{
    public function render();
    public function getGrid();
    public function setGrid(FinalView_Grid $grid);
}