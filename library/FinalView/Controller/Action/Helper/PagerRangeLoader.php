<?php

class FinalView_Controller_Action_Helper_PagerRangeLoader
    extends Zend_Controller_Action_Helper_Abstract
{

    public function direct(Doctrine_Pager $pager, $rangeStyle = null, array $options = array())
    {
        return $this->loadPagerRange($pager, $rangeStyle, $options);
    }

    public function loadPagerRange(Doctrine_Pager $pager, $rangeStyle = null, array $options = array())
    {
        if (is_null($rangeStyle)) {
            $rangeStyle = $this->_getDefaultRangeStyle();
        }
        $options = array_merge($this->_getDefaultOptions(), $options);

        return $pager->getRange($rangeStyle, $options);
    }

    private function _getDefaultRangeStyle()
    {
        $options = $this->_getConfigOptions();
        return $options['pager']['range-style'];
    }

    private function _getDefaultOptions()
    {
        $options = $this->_getConfigOptions();
        return array('chunk' => $options['pager']['range-chunk']);
    }

    private function _getConfigOptions()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('config')->get('doctrine');
    }

}