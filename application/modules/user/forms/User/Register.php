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
        
        $element = new Zend_Form_Element_Password('password_confirm');
        $element
            ->setLabel('Confirm Password')
            ->setRequired()
            ->addValidator('StringLength', false, array(
                    $min_password_length, $max_password_length))
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
    
    /**
     * Validate the form
     * 
     * @param  array $data 
     * @return boolean
     */
    public function isValid($data)
    {
        if ($this->getElement('password') && 
            $this->getElement('password_confirm')) 
        {
            $password_validator = new Zend_Validate_Identical($data['password']);
            $password_validator->setMessage(self::NOT_UNIQUE_PASSWORDS, 
                Zend_Validate_Identical::NOT_SAME);
            
            $element = $this->getElement('password_confirm');
            $element->addValidator($password_validator);
        }
        
        
        return parent::isValid($data);
    }       
}