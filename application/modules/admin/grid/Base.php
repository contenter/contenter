<?php
class Admin_Grid_Base extends FinalView_Grid
{
    public function __construct($params)
    {
        $params = self::getParams() + $params;
        parent::__construct();

        $_params = array();
        if (isset($params['sort'])) {
            $_params['order_by'] = array(
                'field'     =>  $params['sort'],
                'direction' =>  isset($params['direction']) ? $params['direction'] : 'ASC'
            );
        }

		if (isset($params['filter'])) {
            $_params = array_merge( $params['filter'], $_params );
        }
        
        $iterator = Doctrine::getTable($params['model'])->findPageByParams(
            $_params,
            intval(@$params['page']) ? intval(@$params['page']) : 1,
            array_key_exists('entries_per_page', $params)
				? $params['entries_per_page']
				: FinalView_Config::get('admin', 'entries_per_page')
        );        
        
        $this->setIterator($iterator->execute());
        $this->setColumnsFromIterator();
        
        $this->addColumn(
            new FinalView_Grid_Column_Checkbox('ids', 'id'),
            FinalView_Grid_ColumnsCollection::APPEND_FIRST
        );
        
        $this->addPlugin(new FinalView_Grid_Plugin_Pager(
            $iterator->getNumResults(),
            $iterator->getMaxPerPage(), 
            $iterator->getPage()
        ));
        
        $this->addPlugin(new FinalView_Grid_Plugin_Sortable());
        
        switch (true) {
            case isset($_params['order_by']):
                $sortParams = $_params['order_by'];
            break;
            case !is_null($orderBy = Doctrine::getTable($params['model'])->getOption('orderBy')):
                $s_params = explode(' ', $orderBy, 2);
                $sortParams['field'] = $s_params[0];
                $sortParams['direction'] = $s_params[1] == 'desc' ? 'desc' : 'asc';
            break;
            case isset($params['filter']['order_by']):
                $sortParams = $params['filter']['order_by'];
            break;
        }
        
        if (isset($sortParams)) {
            $this->getPlugin('sortable')->setSortParams($sortParams);
        }
    }

    static public function getParams()
    {
        return array_intersect_key(
            Zend_Controller_Front::getInstance()->getRequest()->getParams(),
            array_flip(array('sort', 'direction', 'page'))
        );
    }
}
