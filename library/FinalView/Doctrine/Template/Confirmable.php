<?php
class FinalView_Doctrine_Template_Confirmable extends Doctrine_Template
{    
   
    public function setTableDefinition()
    {       
        
        $listener = new FinalView_Doctrine_Listener_Confirmable();
        
        if ($this->getOption('confirmed', null)) {
            $this->hasColumn('confirmed', 'integer', 1, array(
                 'notnull' => true,
                 'default' => 0
            ));                        	
        }
        
        if ($this->getOption('replied_at', null)) {
            $this->hasColumn('replied_at', 'timestamp');         	
        }
        
        $this->addListener($listener);
    }
    
    public function hasConfirmed()
    {
        return $this->getOption('confirmed', false);
    }
    
    public function hasRepliedAt()
    {
        return $this->getOption('replied_at', false);
    }    
}
