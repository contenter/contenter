<?php
abstract class FinalView_Grid_Decorator_Abstract implements FinalView_Grid_Decorator_Interface
{
    protected $_postRenderContent = '';
    protected $_uri = '';
    
    protected $_grid;
    
    public function __construct($grid)
    {
        $this->setGrid($grid);
    }
    
    public function setGrid($grid)
    {
        $this->_grid = $grid;
    }
    
    public function getGrid()
    {
        return $this->_grid;
    }
    
    public function setUri($uri) 
    {
        $this->_uri = $uri;
    }

    public function getUri() 
    {
        return $this->_uri;
    }
    
    public function addPostRenderContent($content)
    {
        $this->_postRenderContent .= $content;
    }
    
    public function renderPager(Doctrine_Pager $pager) {}
}