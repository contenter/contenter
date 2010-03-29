<?php

/**
* Pagination
* 
*/
class FinalView_View_Helper_SimplePager extends Zend_View_Helper_Abstract
{
    private $page;
    private $perPage;
    private $total;
    private $lastPage;
    
    private $currentPage;
    
    
    public function simplePager($total, $perPage, $page) 
    {
        
        $this->page = $page;
        
        $this->perPage = $perPage;
        
        $this->total = $total;
        
        $this->currentPage = $this->page;
        
        $this->lastPage = null;
        
        $i = 1;
        $pages[1] = true;
        //if($this->getLastPage() >= 2) $pages[2] = true;
        if($this->prevPage() >= 1) $pages[$this->prevPage()] = true;
        $pages[$this->currentPage] = true;
        if($this->nextPage() <= $this->getLastPage()) $pages[$this->nextPage()] = true;
        //if($this->getLastPage() > 1) $pages[$this->getLastPage() - 1] = true;
        $pages[$this->getLastPage()] = true;
        ksort($pages);      
        $lastDrawPage = 0;
        
        $html = '<div class="p-b"><ul>';
        if ($this->currentPage > 1) {
            $html .= '<li class="prev"><a href="' . $this->_makeUrl($this->prevPage()) . '">previous</a></li>';
        }

        foreach($pages as $pageNum => $val){
            if($pageNum > $lastDrawPage + 1){                
                $html .= '<li>...</li>';
            }
            $active = $pageNum == $this->page ? ' class="active"' : '';
            $html .= '<li' . $active . '><a href="' . $this->_makeUrl($pageNum) . '">' . $pageNum . '</a></li>';
            $lastDrawPage = $pageNum;
        }
        if ($this->currentPage < $this->getLastPage()){
            $html .= '<li class="next"><a href="' . $this->_makeUrl($this->nextPage()) . '">next</a></li>';
        }
        
        return $html;
    }
  
    public function getLastPage()
    {
        if ($this->lastPage !== null) return $this->lastPage;
        
        $this->lastPage = ceil($this->total / $this->perPage);
        if ($this->lastPage <= 0) $this->lastPage = 1;
        
        return $this->lastPage; 
    }
  
   
    public function prevPage()
    {
        return $this->currentPage - 1;
    }
  
    public function nextPage()
    {
        return $this->currentPage + 1;
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
