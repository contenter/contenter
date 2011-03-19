<?php

class User_Form_Webmoney extends Zend_Form_SubForm
{
     public function init()
    {

           //Z855482044015
          $this->setElementsBelongTo("gateway");

         $purses_list = array();
         $purses_list['Z'] = 'Z purse(USD) *';
         $purses_list['R'] = 'R purse(RUR)';
         $purses_list['E'] = 'E purse(EURO)';
         $purses_list['U'] = 'U purse(HRN)';
         //dump($gateways->toArray());
        $element = new Zend_Form_Element_Select('type');
        $element
            ->setLabel('Type')
            ->setRequired()
            ->addMultiOptions(array($purses_list))
            ->addFilters(array('StringTrim'))
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Text('purse');
        $element
            ->setLabel('Purse (Digits only)')
            ->setRequired()
            ->addFilters(array('StringTrim'))
        //    ->StringLength(array('max' => 11,'max' => 12))
            ->addValidator('digits', false, array())
            ;
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('amount');
        $element
            ->setLabel('Amount')
            ->setRequired()
            ->addFilters(array('StringTrim'))
       //     ->StringLength(array('max' => 1,'max' => 12))
            ->addValidator('digits', false, array())
            ;
        $this->addElement($element);
    }

}
