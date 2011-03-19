<?php

class User_Form_Page extends Zend_Form
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
        $element = new Zend_Form_Element_Text('uri');
        $element
            ->setLabel('URL_FIELD_LABEL')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator(new FinalView_Validate_Uri())
            ;
        $this->addElement($element);
        
        // Submit
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('SUBMIT_BUTTON_LABEL')
            ->setIgnore(true)
            ;
        $this->addElement($element);
    }
    
    public function isValidResponse(Zend_Http_Response $response)
    {
        $valid = !$response->isError();
        return $valid;
    }
    
}
