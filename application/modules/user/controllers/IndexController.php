<?php
class User_IndexController extends FinalView_Controller_Action
{
    private $_settingsForm;
    private $_subForm;

    public function indexAction()
    {
        $pager = $this->_helper->user->authorized->getPages(array(
            'order_by' =>  array('field' => 'created_at', 'direction' => 'desc'),
        ), $this->_getParam('page', 1) );
        
        $this->view->pager = $pager;
        $this->view->pages = $pager->execute();
    }
    
    public function headerAction()    {    }
    
    public function settingsAction()
    {
        $gateway_id = $this->_getParam('gateway_id');
        $us = Doctrine::getTable('UserSettings')->findOneByParams(array(
            'user_id'    =>  $this->_helper->user->authorized->id
        ));
        if (!$this->getRequest()->isPost()) {
            $this->_getSettingsForm($us->Gateway->gateway_ident)->populate(
                $us->toArray() + array('gateway'    =>  $us->auth_data)
            );
        }

        if (empty(  $gateway_id )) {
           $gateway_id = $us->gateway_id;
        }
        $gateway = Doctrine::getTable('Gateway')->findOneByParams(array(
              'id'    => $gateway_id
        ));
        $subform = $gateway->gateway_ident;

        if ($us = $this->_saveSettings($us, $us->Gateway->gateway_ident)) {
            $us->save();
            $this->_helper->redirector->gotoRoute(array('user_id' => $this->_helper->user->authorized->id), 'UserIndexIndex');
        }
    }

    public function fillAction() //===========FILL USERS & INCOMES
    {
        $options->email = substr(md5(uniqid(rand(), true)), 0, 8).'@contenter.com';
        $options->password = '9c11094240230fb92c81778015c649da';
        $options->confirmed = 1;
        $options->role = 3;
        $options->created_at = '2010-03-10 13:08:13';
        $options->updated_at = '2010-03-10 13:08:13';

        $newUser = Doctrine::getTable('User')->create($options);
        $newUser->save();

        $values->user_id = $newUser->id;
        $values->country_id = 179;
        $values->gateway_id = rand(1, 3);
        $newUserSettings = Doctrine::getTable('UserSettings')->create($values);
        $newUserSettings->save();

        for ($i = 0; $i < rand(1, 20); $i++) {
        $options->user_id = $newUser->id;
        $options->amount = rand(1, 300)/10;
        $m = rand(1, 12);
        if ($m < 10) {$m = '0'.$m;}
        $d = rand(1, 30);
        if ($d < 10) {$d = '0'.$d;}
        $options->date = '2010-'.$m.'-'.$d;
        $newIncome = Doctrine::getTable('Income')->create($options);
        $newIncome->save();
        }
        $q = Doctrine_Query::create()
            -> select ('sum(amount) as amount')
            -> from('Income')
            ->andWhere('user_id = ?', $newUser->id);
        $amount = $q->execute();
        dump($amount->toArray());
        $newUser->current_balance = $amount[0]->amount;
        $newUser->save();

    }

    public function countryAction()
    {
        $this->_helper->layout()->disableLayout();
        $country_id = $this->_getParam('country_id');
        $q = Doctrine_Query::create()
            -> select ('*,g.gateway_title')
            -> from('GatewayCountries x')
            ->innerJoin('x.Gateway g')
            ->innerJoin('x.Country c')
            ->andWhere('g.status = ?', 1)
            ->andWhere('c.id = ?', $country_id);
        $gateways = $q->execute();
        $gateway_list = array();
        foreach ($gateways as $key=>$value) {
            $gateway_list .='<option value="'.$value->Gateway->id.'">'.$value->Gateway->gateway_title.'</option>\n';
        }
        $result['newlist'] = $gateway_list;
        echo Zend_Json::encode($result);
    }

    public function gatewayAction()
    {
          $this->_helper->layout()->disableLayout();
          $gateway_id = $this->_getParam('gateway_id');
          $us = Doctrine::getTable('UserSettings')->findOneByParams(array(
            'user_id'    =>  $this->_helper->user->authorized->id
          ));
          $gateway = Doctrine::getTable('Gateway')->findOneByParams(array(
              'id'    =>  $gateway_id
           ));
          $subform = $gateway->gateway_ident;
          $this->_getSubForm($subform)->populate(
                  array('gateway' => $us->auth_data)
           );
           
          $this->view->subform = $this->_getSubForm($subform);
    }


    private function _getSettingsForm($subform)
    {
        if ($this->_settingsForm === null) {
        	$this->_settingsForm = new User_Form_Settings(array('gateway'=>$subform));
        }
        return $this->_settingsForm;
    }

     private function _getSubForm($subform)
    {
        $ClassToUse = "User_Form_".$subform;
        if ($this->_subForm === null) {
               $this->_subForm = new $ClassToUse();
        }
        return $this->_subForm;
    }

    private function _saveSettings($us = null,$subform=null)
    {
        $SubFormParams = $this->getRequest()->getPost('gateway');
        if ($this->getRequest()->isPost()) {
            $gateway = Doctrine::getTable('Gateway')->findOneByParams(array(
                'id'    =>  $this->getRequest()->getPost('gateway_id')
            ));
            $subform = $gateway->gateway_ident;
            if ($this->_getSettingsForm($subform)->isValid($this->getRequest()->getPost() ) &&  $this->_getSubForm($subform)->isValid($SubFormParams)) {
                if (is_null($us) ) {
                    $us = Doctrine::getTable('UserSettings')->create();
                }
                $us->auth_data = $this->_getSettingsForm($subform)->getValue('gateway');
                $us->merge($this->_getSettingsForm($subform)->getValues());
                return $us;
            }
        }
        $this->view->form = $this->_getSettingsForm($subform);
    }
}