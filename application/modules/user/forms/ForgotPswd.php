<?php

class User_Form_ForgotPswd extends Zend_Form
{

    public function init()
    {
        $element = new Zend_Form_Element_Text('email');
        $element
            ->setLabel('EMAIL_FIELD_LABEL')
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
            ->setLabel('SEND_REQUEST_BUTTON_TEXT')
            ->setIgnore(true)
            ;
        $this->addElement($element);
    }

}
