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
        
    }

    static public function getParams()
    {
        return array_intersect_key(
            Zend_Controller_Front::getInstance()->getRequest()->getParams(),
            array_flip(array('sort', 'direction', 'page'))
        );
    }
}
