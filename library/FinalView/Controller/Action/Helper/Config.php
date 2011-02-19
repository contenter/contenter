<?php

/**
 * Access to the main file configuration params
 *
 * @author dV
 */
class FinalView_Controller_Action_Helper_Config extends Zend_Controller_Action_Helper_Abstract
{

    public function direct($param)
    {
        return $this->get($param);
    }

    public function get($param)
    {
        return $this->getFrontController()->getParam('bootstrap')->getOption($param);
    }

}