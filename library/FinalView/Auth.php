<?php
class FinalView_Auth extends Zend_Auth
{
    
    private static $_table;
        
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }    
    
    public static function setAuthEntityTable($table)
    {
        if (($connection = Doctrine_Manager::getInstance()->getConnectionForComponent($table)) === false) {
            throw new FinalView_Auth_Exception('Not found component '. $table);
        }
        self::$_table = $table;
    } 
    
    public function getAuthEntity($params = array())
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            return Doctrine::getTable(self::$_table)->findOneByParams(array(
                'auth'  =>  Zend_Auth::getInstance()->getStorage()->read()
            ) + $params);        	
        }
        
        return null;
    }
}
