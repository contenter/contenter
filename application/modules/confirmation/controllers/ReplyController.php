<?php
/**
* Reply Controller 
* 
*/
require_once dirname(__FILE__) . '/../Controller.php';

class Confirmation_ReplyController extends Confirmation_Controller
{
    /*
        Here you can write your custom confirmation action 
        Ex: 
        public function userAction() //action name format: <model_to_confirm>Action
        {
            if ($this->getRequest()->getParam('reply') == self::ACTION_ACCEPT) {
                $this->_getConfirmation()->accept();	
            }
            
            if ($this->getRequest()->getParam('reply') == self::ACTION_DECLINE) {
                $this->_getConfirmation()->decline();	
            }            
        }
    */      
}
