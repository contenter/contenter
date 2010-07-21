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
        
        $access = Zend_Controller_Action_HelperBroker::getStaticHelper('isAllowed');
        
        $this->addColumn(
            new FinalView_Grid_Column_Action(
                'edit_action', 
                'Edit',
                $router->getRoute('AdminCmsEditPage'), 
                array('page_name' => 'name')
            )
        );
        
        if ($access->isAllowed('delete-cms-pages', array(), 'Cms') ) {
            $this->addColumn(
                new FinalView_Grid_Column_Action(
                    'delete_action', 
                    'Delete',
                    $router->getRoute('AdminCmsDeletePage'), 
                    array('page_name' => 'name')
                )
            );
            
            $this->addPlugin(new FinalView_Grid_Plugin_Colspan(array(
                'edit_action'   =>  2
            )));                    	
        }        
        
        $this->addPlugin(new FinalView_Grid_Plugin_Sortable(array(
            'name', 'title'
        )));
        
        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        
        if ($access->isAllowed('admin-cms-add-page', array(), 'Request' ) ) {
            
            $this->addPlugin(new FinalView_Grid_Plugin_Gridactions(array(
                array('type' => 'link', 'label' =>  'Add Page', 'href' => $urlHelper->url(array(), 'AdminCmsAddPage' ))
            )));
                    	
        }
                                        
    }
}
