<?php
class FinalView_Grid_Plugin_Pager extends FinalView_Grid_Plugin_Abstract
{
    public $name = 'pager';
    
    public $total;
    public $perPage;
    public $page;

    public function __construct($total, $perPage, $page)
    {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->page = $page;
    }
    
    public function init()
    {
    
    }
    
    public function getScriptsPath()
    {
        return dirname(__FILE__).'/scripts/Pager';
    }
}