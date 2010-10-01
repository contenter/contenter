<?php
class Admin_Grid_Users extends Admin_Grid_Base
{
    public function __construct()
    {
        parent::__construct(array(
            'model'     =>  'User',
            'filter'    =>  array(
                'role'  =>  Roles::USER_FRONTEND
            )
        ));

        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $this->addColumn(
            new FinalView_Grid_Column_Action(
                'edit_action', 
                'Edit',
                $router->getRoute('AdminUserEdit'), 
                array('user_id' => 'id')
            )
        );        
        
        $this->addPlugin(new FinalView_Grid_Plugin_Gridactions(array(
            array('type'    =>  'submit', 'value' => 'Delete Users', 'name' => 'delete'),
        )));
        
        $this->addPlugin(new FinalView_Grid_Plugin_Sortable(array(
            'id', 'email', 'created_at', 'updated_at', 'role'
        )));                
        
    }
}
