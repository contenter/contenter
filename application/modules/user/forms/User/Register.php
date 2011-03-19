<?php

class User_Form_User_Register extends User_Form_User_Abstract
{    
    const NOT_UNIQUE_PASSWORDS = 'NOT_UNIQUE_PASSWORDS';    
    
    /**
     * Initialize form (used by extending classes)
     * 
     * @return void
     */
    public $ip;
    public $country_code;

    public function setIp($ip)
    {
        $this->ip = $ip;
    }
    public function setCountry_code($country_code)
    {
        $this->country_code = $country_code;
    }
    
    public function init()
    {
        parent::init();     
        
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('EMAIL_FIELD_LABEL')
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
            ->setLabel('PASSWORD_FIELD_LABEL')
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
            ->setLabel('CONFIRM_PASSWORD_FIELD_LABEL')
            ->setRequired()
            ->addValidator($validate)
            ->setAttrib('renderPassword', true)
            ;
        $this->addElement($element);

        $countries_list = Doctrine::getTable('GeoCountry')->getCountriesAsOptions();

        $element = new Zend_Form_Element_Select('country');
        $element
            ->setLabel("Select your country")
            ->setRequired()
            ->addMultiOptions(array($countries_list))
            ->setValue($this->country_code)
            ->addFilters(array('StringTrim'))
            ;
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('REGISTER_BUTTON_TEXT')
            ->setIgnore(true)
            ->setOrder(100)
            ;
        $this->addElement($element);
    }
}
