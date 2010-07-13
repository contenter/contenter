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
    
    public function setTitle(FinalView_Grid_Column_Title_Standard $title = null)
    {        
        if ($title === null) {
            $this->_title = new FinalView_Grid_Column_Title_Standard($this);	
        }else{
            $this->_title = $title;
        }        
    }
    
    public function getTitle()
    {
        return $this->_title;
    }    
}
