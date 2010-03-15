<?php

/**
* Pagination
* 
*/
class FinalView_View_Helper_Pagination extends Zend_View_Helper_Abstract
{
    
    public function pagination(Doctrine_Pager_Range $pager_range, $current_page) 
    {
        $pager = $pager_range->getPager();
        
        if ($pager->getMaxPerPage() < $pager->getNumResults()) {
            $html = '<div class="p-b"><ul>';
            if ($current_page > 1) {
                $html .= '<li class="prev"><a href="' . $this->_makeUrl($current_page - 1) . '">previous</a></li>';
            }
            foreach ($pager_range->rangeAroundPage() as $page) {
                $active = $current_page == $page ? ' class="active"' : '';
                $html .= '<li' . $active . '><a href="' . $this->_makeUrl($page) . '">' . $page . '</a></li>';
            }
            
            if ($current_page < $pager->getLastPage() ) {
                $html .= '<li class="next"><a href="' . $this->_makeUrl($current_page + 1) . '">next</a></li>';
            }            
            
            return $html . '</ul></div>';
        }
        
        return '';   
    }
    
    private function _makeUrl($page) 
    {
        $request_uri_parts = parse_url(Zend_Controller_Front::getInstance()
            ->getRequest()->getRequestUri());
        $path = array_shift($request_uri_parts);
        $query = !empty($request_uri_parts) 
            ? array_shift($request_uri_parts) 
            : '';
        
        parse_str($query, $query_array);
        $query = http_build_query(array_merge(
            $query_array, array('page' => $page)));
        
        
        return $path . '?' . $query;
    }
    
}
