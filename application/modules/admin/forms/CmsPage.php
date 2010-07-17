<?php

class Admin_Form_CmsPage extends Zend_Form
{
    
    /**
     * Initialize form (used by extending classes)
     * 
     * @return void
     */
    public function init()
    {
        $element = new Zend_Form_Element_Text('name');
        $element
            ->setLabel('Name')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ->addValidator('Alpha')
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Text('title');
        $element
            ->setLabel('Title')
            ->setRequired()
            ->addFilters(array('StringTrim'))
            ;
        $this->addElement($element);                
        
        $element = new FinalView_Form_Element_WYSIWYG('contents');
        $element
            ->setLabel('Content')
            ->setRequired()
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Submit')
            ->setIgnore(true)
            ;
        $this->addElement($element);
    }
    
}
