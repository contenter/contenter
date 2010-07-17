<?php
class Admin_Grid_CmsPages extends Admin_Grid_Base
{
    public function __construct()
    {
        parent::__construct(array(
            'model' => 'CmsPage',
        ));

        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $this->getColumns()->removeColumn('ids');
        
        $this->addColumn(
            new FinalView_Grid_Column_Action(
                'edit_action', 
                'Edit',
                $router->getRoute('AdminCmsEditPage'), 
                array('page_name' => 'name')
            )
        );        
        
        $this->addPlugin(new FinalView_Grid_Plugin_Sortable(array(
            'name', 'title'
        )));
        
        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        
        $this->addPlugin(new FinalView_Grid_Plugin_Gridactions(array(
            array('type' => 'link', 'label' =>  'Add Page', 'href' => $urlHelper->url(array(), 'AdminCmsAddPage' ))
        )));                        
        
    }
}
