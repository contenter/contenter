<?php

class User_Form_Paypal extends Zend_Form_SubForm
{
     public function init()
    {
        $this->setElementsBelongTo("gateway");

        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('Paypal email')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('EmailAddress')
            ;
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('amount');
        $element
            ->setLabel('Amount')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            //->StringLength(array('max' => 1,'max' => 12))
            ->addValidator('digits', false, array())
            ;
        $this->addElement($element);
    }

}
