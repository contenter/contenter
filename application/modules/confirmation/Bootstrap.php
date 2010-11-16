<?php

class Confirmation_Bootstrap extends FinalView_Application_Module_Bootstrap
{
    public function init()
    {
        Zend_Controller_Action_HelperBroker::addPath(
            dirname(__FILE__) . '/helpers', 'Confirmation_Action_Helper'
        );
    }
}
