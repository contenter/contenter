<?php
class FinalView_Doctrine_Template_Confirmable extends Doctrine_Template
{    
   
    protected $_confirmations;
    
    public function setTableDefinition()
    {        
        $listener = new FinalView_Doctrine_Listener_Confirmable();
        
        $this->addListener($listener);
    }
    
    public function createConfirmation($type)
    {
        if ($this->getOption($type, false) === false) {
            throw new FinalView_Doctrine_Exception('confirmation type: '. $type . ' is not defined for model: '. $this->getTable()->getComponentName());
        }
        
        $self = $this->getInvoker();
        
        Doctrine::getTable('Confirmation')->createHash(
            $self->getTable()->getComponentName(), 
            $self->getIdentifier(),
            $type
        )->save();
        
        $self->getConfirmation($type);
    }
    
    public function getConfirmation($type)
    {
        if ($this->getOption($type, false) === false) {
            throw new FinalView_Doctrine_Exception('confirmation type: '. $type . ' is not defined for model: '. $this->getTable()->getComponentName());
        }
        
        $self = $this->getInvoker();
        
        if (!isset($this->_confirmations[$type])) {
        	$this->_confirmations[$type] = Doctrine::getTable('Confirmation')->findOneByParams(array(
                'entity' =>  array(
                    'model' =>  $self->getTable()->getComponentName(),
                    'id'    =>  $self->getIdentifier(),
                    'type'  =>  $type
                )
            ) );
        }
        
        return $this->_confirmations[$type];
    }   
}
