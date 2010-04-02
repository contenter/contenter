<?php

/**
* User encrypt utility
* 
*/
class FinalView_Auth_Encrypt 
{ 
    
    /**
    * Encrypt value
    * 
    * @param string $value
    */
    static public function encrypt($value) 
    {
        return md5($value);
    }
    
    /**
    * Compare
    * 
    * @param string $password
    * @param string $encrypted
    */
    static public function compare($password, $encrypted) 
    {
        return md5($password) === (string)$encrypted;
    }
    
}
