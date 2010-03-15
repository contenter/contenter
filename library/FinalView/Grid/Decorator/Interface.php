<?php
interface FinalView_Grid_Decorator_Interface
{
    public function preRender();
    public function postRender();
    public function preRenderRow($index = null);
    public function postRenderRow();
    public function endRender();
    public function addPostRenderContent($content);
    public function renderPager(Doctrine_Pager $pager);
}