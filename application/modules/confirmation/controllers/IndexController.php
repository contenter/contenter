<?php

/**
* Confirmation_Controller 
* 
*/
class Confirmation_IndexController extends FinalView_Controller_Action 
{
    
    const ACTION_ACCEPT = 'ACTION_ACCEPT';
    const ACTION_DECLINE = 'ACTION_DECLINE';
    
    protected $_confirmation;   
        
    public function acceptAction()
    {
        $confirmation = $this->_helper->confirmation;

        $this->_forward($confirmation->confirmation_type, $confirmation->entity_model, 'confirmation', array(
            'hash'  =>  $this->getRequest()->getParam('hash'),
            'reply' =>  self::ACTION_ACCEPT
        ));
    }
    
    public function declineAction()
    {
        $confirmation = $this->_helper->confirmation;
        
        $this->_forward($confirmation->confirmation_type, $confirmation->entity_model, 'confirmation', array(
            'hash'  =>  $this->getRequest()->getParam('hash'),
            'reply' =>  self::ACTION_DECLINE
        ));
    }    
}
