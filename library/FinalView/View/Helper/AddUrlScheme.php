<?php

/**
 * Automatically add given scheme to the given url if not exists
 *
 */
class FinalView_View_Helper_AddUrlScheme extends Zend_View_Helper_Abstract
{

    public function addUrlScheme($url, $scheme = 'http')
    {
        if (is_null(parse_url($url, PHP_URL_SCHEME))) {
            $url = $scheme . '://' . ltrim($url, '/');
        }

        return $url;
    }

}