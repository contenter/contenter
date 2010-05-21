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
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('Email')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('EmailAddress')
            ;
        $this->addElement($element);
        
        extract(FinalView_Config::get('user', 
            array('min_password_length', 'max_password_length')));
        $element = new Zend_Form_Element_Password('password');
        $element
            ->setLabel('Password')
            ->setRequired()
            ->setAttrib('renderPassword', true)
            ->addValidator('StringLength', false, array(
                    $min_password_length, $max_password_length))
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Login')
            ->setIgnore(true)
            ;
        $this->addElement($element);
        $this->loadDefaultDecorators();
        $this->addDecorator('Errors');
        
        $this->getDecorator('Errors')->setOption('placement', Zend_Form_Decorator_Abstract::PREPEND);      
    }
    
}
