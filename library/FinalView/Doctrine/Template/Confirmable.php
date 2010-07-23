<?php
class FinalView_Doctrine_Template_Confirmable extends Doctrine_Template
{    
   
    protected $_confirmations;
    
    public function createConfirmation($type)
    {
        if (!$this->isDefinedType($type)) {
            throw new FinalView_Doctrine_Exception('Not defined confirmation type: ' . $type . 'for model: ' . $this->getTable()->getComponentName());        	
        }
        
        $self = $this->getInvoker();
        
        Doctrine::getTable('Confirmation')->createHash(
            $self->getTable()->getComponentName(), 
            $self->getIncremented(),
            $type
        )->save();
    }
    
    public function getConfirmation($type)
    {
        if (!$this->isDefinedType($type)) {
            throw new FinalView_Doctrine_Exception('Not defined confirmation type: ' . $type . 'for model: ' . $this->getTable()->getComponentName());        	
        }
        
        $self = $this->getInvoker();
        
        return Doctrine::getTable('Confirmation')->findOneByParams(array(
            'entity' =>  array(
                'model' =>  $self->getTable()->getComponentName(),
                'id'    =>  $self->getIncremented(),
                'type'  =>  $type
            )
        ));
    }
    
    public function isDefinedType($type)
    {
        if (($types = $this->getOption('types', false)) === false) {
            throw new FinalView_Doctrine_Exception('Confirmation types is not defined for model: ' . $this->getTable()->getComponentName());
        }
        
        return in_array($type, $types);    
    }
}
