<?php
class Admin_Grid_Users extends FinalView_Grid
{
    public function __construct($params)
    {
        parent::__construct();

        $user_params = array();
        if (isset($params['sort'])) {
            $user_params['order_by'] = array(
                'field'     =>  $params['sort'],
                'direction' =>  (@$params['direction'] == 'asc') ? 'asc' : 'desc'  
            );
        }
        
        $iterator = Doctrine::getTable('User')->findPageByParams(
            $user_params,
            intval(@$params['page']) ? intval(@$params['page']) : 1,
            FinalView_Config::get('admin', 'entries_per_page')           
        );        
        
        $this->setIterator($iterator->execute());
        $this->setColumnsFromIterator();
        
        $this->addColumn(
            new FinalView_Grid_Column_Checkbox('users_ids', 'id'),
            FinalView_Grid_ColumnsCollection::APPEND_FIRST
        );
        
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $this->addColumn(
            new FinalView_Grid_Column_Action(
                'edit_action', 
                'Edit',
                $router->getRoute('AdminUserEdit'), 
                array('user_id' => 'id')
            )
        );        
        
        $this->addPlugin(new FinalView_Grid_Plugin_Pager(
            $iterator->getNumResults(),
            $iterator->getMaxPerPage(), 
            $iterator->getPage()
        ));
        
        $this->addPlugin(new FinalView_Grid_Plugin_Gridactions(array(
            array('type'    =>  'submit', 'value' => 'Delete Users', 'name' => 'delete'),
        )));
        
        $this->addPlugin(new FinalView_Grid_Plugin_Sortable(array(
            'id', 'email', 'created_at', 'updated_at', 'role'
        )));                
        
    }
}
