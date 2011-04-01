<?php

class FinalView_Mail_Template_Doctrine implements FinalView_Mail_Template_Interface
{
    public static $_default_model = 'MailTemplates';
    public static $selector = 'template';

    protected $_template;

    public function __construct($key = null, $model = null)
    {
        $this->_key = $key;
        $this->_model = $model;
    }

    public function getSubject()
    {
        return $this->_getTemplate()->subject;
    }

    public function getBodyText(){
        return $this->_getTemplate()->text;
    }

    public function getBodyHtml(){
        return $this->_getTemplate()->html;
    }
    
    private function _getTemplate()
    {
        if (is_null($this->_template)) {
            $this->_template = $this->getModel()->findOneByParams(array(
                self::$selector     =>  $this->getKey()
            ));
            
            if (!$this->_template) {
                throw new FinalView_Mail_Exception('Template with key ' . $this->getKey() . 'couldn\'t be found');
            }
        }
        
        return $this->_template;
    }
    
    public function setModel($model)
    {
        $this->_model = Doctrine::getTable($model);
        return $this;
    }
    
    public function getModel()
    {
        if (is_null($this->_model)){
            $this->_model = Doctrine::getTable(self::$_default_model);
        }elseif(is_string($this->_model)){
            $this->_model = Doctrine::getTable($this->_model);
        }
        
        return $this->_model;
    }
    
    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->_key;
    }
}