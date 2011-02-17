<?php
/**
 */
class FinalView_Form_DynamicItems extends Zend_Form_SubForm

{
    protected $_defaultItemsCount = 0;
    protected $_itemTemplate;
    protected $_addLinkText;
    protected $_removeLinkText;
    protected $_itemsCount;

    public function init()
    {
        $this->_itemsCount = $this->_defaultItemsCount;
        if ($this->_defaultItemsCount > 0) {
            if (is_null($this->_itemTemplate)) {
                throw new FinalView_Form_Exception('itemTemplate must be defined if defaultItemsCount more than 0');
            }
            for ($i = 0; $i < $this->_defaultItemsCount; $i++) {
                $defForm = clone($this->_itemTemplate);
                $defForm->removeDecorator('FinalView_Form_Decorator_RemoveItemLink');
                $this->addSubForm($defForm, $i);
            }
        }
        
        parent::init();
    }

    public function setDefaultItemsCount($count)
    {
        $this->_defaultItemsCount = $count;
        return $this;
    }
    
    public function getDefaultItemsCount($count)
    {
        return $this->_defaultItemsCount;
    }
    
    public function setItemTemplate($template)
    {
        if (is_array($template)) {
            $this->_itemTemplate = new Zend_Form_SubForm($template);
        }elseif($template instanceof Zend_Form) {
            $template->removeDecorator('Form');
            $this->_itemTemplate = $template;
        }else{
            throw new FinalView_Form_Exception('Template must be array or instance of Zend_Form');
        }
        
        $this->_itemTemplate->removeDecorator('DtDdWrapper');
        $this->_itemTemplate->removeDecorator('Fieldset');
        $this->_itemTemplate->addDecorator(new FinalView_Form_Decorator_RemoveItemLink(array('linkText' => $this->getRemoveLinkText() )));
        $this->_itemTemplate->addDecorator('Fieldset');
        $this->_itemTemplate->addDecorator('DtDdWrapper');
        
        return $this;
    }
    
    public function getItemTemplate()
    {
        return $this->_itemTemplate;
    }
    
    public function setDefaults(array $defaults)
    {
        foreach ($defaults[$this->getName()] as $key=>$value) {
            if ($item = $this->getSubForm($key)) {
                $item->setDefaults($value);
            }else{
                $item = clone($this->_itemTemplate);
                $item->setDefaults($value);
                $this->addSubForm($item, $key);
                $this->_itemsCount++;
            }
        }
    }
    
    public function setAddLinkText($text)
    {
        $this->_addLinkText = $text;
        return $this;
    }
    
    public function getAddLinkText()
    {
        return $this->_addLinkText;
    }
    
    public function setRemoveLinkText($text)
    {
        $this->_removeLinkText = $text;
        return $this;
    }

    public function getRemoveLinkText()
    {
        return $this->_removeLinkText;
    }
    
    public function getItemsCount()
    {
        return $this->_itemsCount;
    }
    
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator(new FinalView_Form_Decorator_AddItemLink(array('linkText' => $this->getRemoveLinkText() )))
                 ->addDecorator('Fieldset')
                 ->addDecorator('DtDdWrapper');
        }
        return $this;
    }
    
    public function isValid($data)
    {
        $this->setDefaults($data);
        
        return parent::isValid($data);
    }
}
