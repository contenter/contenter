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

        $this->_forward($confirmation->entity_model, 'reply', 'confirmation', array(
            'hash'  =>  $this->getRequest()->getParam('hash'),
            'reply' =>  self::ACTION_ACCEPT
        ));
    }
    
    public function declineAction()
    {
        $confirmation = $this->_getConfirmation();
        
        $this->_forward($confirmation->entity_model, 'reply', 'confirmation', array(
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
        
    public function __call($func, $args)
    {
        if ('Action' == substr($func, -6)) {
            $this->view->reply_type = $this->getRequest()->getParam('reply', null);
            $this->view->entity = $this->_getConfirmation()->Entity;
            
            switch ($this->getRequest()->getParam('reply', null) ) {
                case self::ACTION_ACCEPT:
                    $this->_getConfirmation()->accept();    
                break;
                case self::ACTION_DECLINE:
                    $this->_getConfirmation()->decline();
                break;
                default:
                    parent::__call($func, $args);
                break;
            }
        }else{
            parent::__call($func, $args);
        }
    }
    
}
