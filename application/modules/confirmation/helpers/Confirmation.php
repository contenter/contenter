<?php

class Confirmation_Action_Helper_Confirmation
    extends Zend_Controller_Action_Helper_Abstract
{
    private $_confirmation;

    public function direct($reload = false)
    {
       return $this->getConfirmation($reload);
    }

    public function getConfirmation($reload = false)
    {
        if (is_null($this->_confirmation) || $reload === true) {
            $hash = $this->getRequest()->getParam('hash');

            $this->_confirmation = Doctrine::getTable('Confirmation')->findOneByParams(array(
                'hash'  =>  $this->getRequest()->getParam('hash')
            ));
        }

        return $this->_confirmation;
    }

}
