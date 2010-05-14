<?php

/**
* Confirmation_Controller 
* 
*/
class Confirmation_Controller extends FinalView_Controller_Action 
{
    
    const ACTION_ACCEPT = 'ACTION_ACCEPT';
    const ACTION_DECLINE = 'ACTION_DECLINE';
    
    protected $_confirmation;   
        
    public function acceptAction()
    {
        $confirmation = $this->_getConfirmation();

        $this->_forward($confirmation->confirmation_type, $confirmation->entity_model, 'confirmation', array(
            'hash'  =>  $this->getRequest()->getParam('hash'),
            'reply' =>  self::ACTION_ACCEPT
        ));
    }
    
    public function declineAction()
    {
        $confirmation = $this->_getConfirmation();
        
        $this->_forward($confirmation->confirmation_type, $confirmation->entity_model, 'confirmation', array(
            'hash'  =>  $this->getRequest()->getParam('hash'),
            'reply' =>  self::ACTION_DECLINE
        ));
    }
    
    
    protected function _getConfirmation()
    {
        if (is_null($this->_confirmation)) {
        	$this->_confirmation = Doctrine::getTable('Confirmation')->findOneByParams(array(
                'hash'  =>  $this->getRequest()->getParam('hash')
            ));
        }
        
        return $this->_confirmation;
    }    
}
