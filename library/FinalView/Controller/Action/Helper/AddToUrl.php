<?php

/**
 * Add To Url
 *
 * @author Andrey M
 *
 */
class FinalView_Controller_Action_Helper_AddToUrl extends Zend_Controller_Action_Helper_Abstract
{

    public function direct(array $params = array(), $url = null)
    {
        return $this->addToUrl($params, $url);
    }

    public function addToUrl(array $params = array(), $url = null)
    {
        if (is_null($url)) {
            $url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        }

        parse_str(parse_url($url, PHP_URL_QUERY), $query);

        $parts = array('query' => array_merge($query, $params));
        return empty($parts['query']) ? $url : http_build_url($url, $parts);
    }

}
