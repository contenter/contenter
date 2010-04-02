<?php

class User_Form_User_Register extends User_Form_User_Abstract
{    
    const NOT_UNIQUE_PASSWORDS = 'NOT_UNIQUE_PASSWORDS';    
    
    /**
     * Initialize form (used by extending classes)
     * 
     * @return void
     */
    public function init()
    {
        parent::init();     
        
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('Email')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('EmailAddress')
            ;
        $element->addValidator(
            new FinalView_Validate_Db_NoRecordExists('User', 'email'));
        
        $this->addElement($element);
        
        extract(FinalView_Config::get('user', 
            array('min_password_length', 'max_password_length')));
        $element = new Zend_Form_Element_Password('password');
        $element
            ->setLabel('Password')
            ->setRequired()
            ->addValidator('StringLength', false, array(
                    $min_password_length, $max_password_length))
            ->setAttrib('renderPassword', true)
            ;
        $this->addElement($element);
        
        // password confirm
        $validate = new FinalView_Validate_ContextIdentical('password');
        
        $element = new Zend_Form_Element_Password('password_confirm');
        $element
            ->setLabel('Confirm Password')
            ->setRequired()
            ->addValidator($validate)
            ->setAttrib('renderPassword', true)
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Register')
            ->setIgnore(true)
            ->setOrder(100)
            ;
        $this->addElement($element);
    }
}
