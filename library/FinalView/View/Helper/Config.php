<?php

class FinalView_View_Helper_Config extends Zend_View_Helper_Abstract
{

    public function config($param)
    {
        return Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getOption($param);
    }

}