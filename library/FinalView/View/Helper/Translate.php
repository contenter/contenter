<?php

class FinalView_View_Helper_Translate extends Zend_View_Helper_Translate
{
    
    /**
    * Self instance
    * 
    * @var FinalView_View_Helper_Translate
    */
    static private $_instance;
    
    /**
     * Constructor for manually handling
     *
     * @param Zend_Translate|Zend_Translate_Adapter $translate Instance of Zend_Translate
     */
    public function __construct($translate = null)
    {
        parent::__construct($translate);
        
        self::$_instance = $this;
    }
    
    /**
    * Return self instance
    * 
    * @return FinalView_View_Helper_Translate
    */
    static public function getInstance() 
    {
        if (is_null(self::$_instance)) {
            throw new Zend_View_Exception('Instance is not set yet');
        }
        
        return self::$_instance;
    }
    
}

/**
 * Shortcut for FinalView_View_Helper_Translate::translate(). 
 *
 * @param   string  $phrase [, mixed $param1, …]
 * @return  string
 */
function __($message) 
{
    $args = func_get_args();
    $translator = FinalView_View_Helper_Translate::getInstance();
    
    return call_user_func_array(array($translator, 'translate'), $args);
}