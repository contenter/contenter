<?php

/**
* Pagination
*
*/
class FinalView_View_Helper_Paging extends Zend_View_Helper_Abstract
{
    public static $script;

    public $perPage;
    public $total;
    public $lastPage;

    public $currentPage;

    private $_pages = array();


    public static function setScript($script)
    {
        self::$script = $script;
    }

    public function paging($total, $perPage, $page)
    {
        $this->perPage = $perPage;
        $this->total = $total;
        $this->currentPage = is_null($page)?1:$page;

        $this->lastPage = null;
        $this->pages = array();

        $this->_setPages();

        if (!is_null(self::$script) ) {
            return $this->view->partial(
                self::$script,
                array('paging'    =>  $this)
            );
        }

        return $this->_renderPages();
    }

    private function _renderPages()
    {
        $lastDrawPage = 0;

        $html = '<div class="paging"><ul>';
        if ($this->currentPage > 1) {
            $html .= '<li class="prev"><a href="' . $this->_makeUrl($this->prevPage()) . '">previous</a></li>';
        }

        foreach($this->_pages as $pageNum => $val){
            if($pageNum > $lastDrawPage + 1){
                $html .= '<li>...</li>';
            }
            $active = $pageNum == $this->currentPage ? ' class="active"' : '';
            $html .= '<li' . $active . '><a href="' . $this->_makeUrl($pageNum) . '">' . $pageNum . '</a></li>';
            $lastDrawPage = $pageNum;
        }
        if ($this->currentPage < $this->getLastPage()){
            $html .= '<li class="next"><a href="' . $this->_makeUrl($this->nextPage()) . '">next</a></li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

    private function _setPages()
    {
        $this->_pages[1] = true;

        if($this->prevPage() >= 1) $this->_pages[$this->prevPage()] = true;
        $this->_pages[$this->currentPage] = true;
        if($this->nextPage() <= $this->getLastPage()) $this->_pages[$this->nextPage()] = true;

        $this->_pages[$this->getLastPage()] = true;

        ksort($this->_pages);
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
