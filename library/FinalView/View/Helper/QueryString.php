<?php

class FinalView_View_Helper_QueryString extends Zend_View_Helper_Abstract
{
    
    public function queryString($url, $query) 
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $query_old);
        is_array($query) ? $query_new = $query : parse_str($query, $query_new);

        return http_build_url($url, array('query' => $query_old + $query_new));
    }
    
}