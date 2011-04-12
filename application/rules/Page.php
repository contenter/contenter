<?php
class Application_Rules_Page extends FinalView_Access_Rules_Abstract
{
    protected $_page;

    public function pageExistRule()
    {
        $this->_page = Doctrine::getTable('Page')->findOneByParams(array(
            'id'    =>  $this->_params['page_id']
        ));

        return (bool)$this->_page;
    }

    public function pageOwnerRule()
    {
        $userHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('User');
        return $this->_page->user_id == $userHelper->authorized->id;
    }
}
