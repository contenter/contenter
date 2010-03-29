<?php
class FinalView_Grid_Column extends FinalView_Grid_Entity_Abstract
{
           
    protected $_title;
    
    public function __construct($name, $script)
    {        
        $this->setName($name);
        
        $this->_script = 'column/'.$script;
        
        $this->setTitle();                    
    }
    
    public function setTitle()
    {
        $this->_title = new FinalView_Grid_Column_Title_Standard($this);
    }
    
    public function getTitle()
    {
        return $this->_title;
    }    
}