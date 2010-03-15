<?php

interface FinalView_Mail_Interface
{
    
    /**
    * Set template
    * 
    * @param string $template
    */
    public function setTemplate($template);
    
    /**
    * Set template vars
    * 
    * @param array $vars
    */
    public function setVars(array $vars);
    
    /**
    * Set model
    * 
    * @param string $model
    */
    public function setModel($model);
    
    /**
    * Send email
    * 
    * @param string|array $email
    * @param string $name
    */
    public function send($email = null, $name = '');
    
}