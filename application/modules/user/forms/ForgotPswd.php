<?php

class User_Form_ForgotPswd extends Zend_Form 
{
    
    public function init()
    {
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('Email')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('EmailAddress')
            ;
            
        $element->addValidator(
            new FinalView_Validate_Db_RecordExists('User', 'email')
        );            
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Send Request')
            ->setIgnore(true)
            ;
        $this->addElement($element);
    }
    
}
