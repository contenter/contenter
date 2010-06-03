<?php

class User_Form_Login extends Zend_Form 
{
    
    /**
     * Initialize form (used by extending classes)
     * 
     * @return void
     */
    public function init()
    {
        parent::init();
        
        // Email
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('Email')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('EmailAddress')
            ;
        $this->addElement($element);
        
        // Password
        $min_password_length = null; 
        $max_password_length = null;
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
        
        // Submit
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Login')
            ->setIgnore(true)
            ;
        $this->addElement($element);
        
        // Decorators
        $this->addPrefixPath('FinalView_Form_Decorator', 'FinalView/Form/Decorator', Zend_Form::DECORATOR);
        $this->loadDefaultDecorators();
        $this->addDecorator('FvformErrors');
        $this->getDecorator('FvformErrors')->setOption('placement', Zend_Form_Decorator_Abstract::PREPEND);      
    }
    
}
