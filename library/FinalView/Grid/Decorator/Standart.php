<?php
class FinalView_Grid_Decorator_Standart extends FinalView_Grid_Decorator_Abstract
{
    
    public function preRender()
    {
        echo '<form action="" id="grid_form" method="post" class="fv_gridform" onsubmit="retrun false;">'.PHP_EOL;
        echo '<table border="0" cellpadding="0" cellspacing="0" class="fv_gridtable" >'.PHP_EOL;
    }
    
    public function postRender()
    {
        echo '</table></form>'.PHP_EOL;
    }
    
    public function endRender()
    {
        if (!empty($this->_postRenderContent)) {
            echo $this->_postRenderContent;
        }
    }
    
    public function preRenderRow($index = null)
    {
        $class = !is_null($index) 
            ? ((int)$index % 2 ? 'odd' : 'even')
            : '';
        echo '<tr class="fv_gridrow ' . $class . '">'.PHP_EOL;
    }
    
    public function postRenderRow()
    {
        echo '</tr>'.PHP_EOL;
    }
    
    private function getPageUri($page)
    {
        return rtrim($this->getUri(), '/').'/page/'.$page.$this->getQueryString();
    }
    
    private function getQueryString()
    {
        $uriParams = $this->getGrid()->getUriParams();
        if (!empty($uriParams)) {
        	return '?'.http_build_query($this->getGrid()->getUriParams());
        }
        return '';
    }
    
    public function renderPager(Doctrine_Pager $pager)
    {
        if (!$pager->haveToPaginate()) return '';
        
        echo '<div class="fv_pager">';
        
        $pagerRange = $pager->getRange('Sliding', array( 'chunk' => 5 ));
        $pages = $pagerRange->rangeAroundPage();

        if (!in_array(1, $pages)) {
            echo '<a href="'.$this->getPageUri($pager->getFirstPage()).'">'.$pager->getFirstPage().'</a>';
        }
        
        foreach ($pages as $p) {
            if ( $p == $pager->getPage()) {
                echo '<a class="fv_current_page">'.$p.'</a>';
            } else {
                echo '<a href="'.$this->getPageUri($p).'">'.$p.'</a> ';
            }
        }
        
        if (!in_array($pager->getLastPage(), $pages)) {
            echo '<a href="'.$this->getPageUri($pager->getLastPage()).'">'.$pager->getLastPage().'</a>';
        }
        
        echo '</div>';
    }
}