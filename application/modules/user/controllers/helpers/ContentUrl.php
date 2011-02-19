<?php

/**
 * Add To Url
 *
 * @author Andrey M
 *
 */
class User_ActionHelper_ContentUrl extends Zend_Controller_Action_Helper_Abstract
{

	public function direct($scope = 'private', array $params = array(), $routeName)
    {
        return $this->_buildUrl($scope, $params, $routeName);
    }

    public function _buildUrl($scope = 'private', array $params = array(), $routeName)
    {
        $schema = 'http://';

        $domains = $this->getFrontController()->getParam('bootstrap')->getOption('domains');

        if (!array_key_exists($scope, $domains)) {
            throw new Zend_Controller_Action_Exception('Not defined domain scope in config', 503);
        }

        $uri = Zend_Controller_Action_HelperBroker::getStaticHelper('Url')->url($params, $routeName);

        return $schema . trim($domains[$scope], '/') . $uri;
    }

}
