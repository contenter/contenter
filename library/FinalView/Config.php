<?php

/**
* This is fucking hell!
* 
* @example "#2 "QUOTE"Priority Listing"QUOTE"" -> #2 "Priority Listing"
*/
define('QUOTE', '"');

abstract class FinalView_Config 
{
    
    const CONFIG_FILENAME = 'config.ini';
    
    /**
    * Modules config params
    * 
    * @var array
    */
    static private $_cach = array();
    
    /**
    * Return config value(s)
    * 
    * @example 
    * 1. param is string then return $value or $default|null
    * 2. param is index of array then return $values or $default|null
    * 3. param is associated array then return $values or default values (values of given array)
    * 
    * @param string $module
    * @param mixed $param
    * @param mixed $default
    * @return mixed
    */
    static public function get($module, $param, $default = null) 
    {
        $module = strtolower($module);
        
        if (!array_key_exists($module, self::$_cach)) {
            self::_load($module);
        }
        
        $config = self::$_cach[$module];
        $output = array();
        if (is_array($param)) {
            foreach ($param as $param_or_index => $default_or_param) {
                is_numeric($param_or_index) 
                    ? (array_key_exists($default_or_param, $config) 
                        ? $output[$default_or_param] = $config[$default_or_param] 
                        : $output[$default_or_param] = $default)
                    : (array_key_exists($param_or_index, $config) 
                        ? $output[$param_or_index] = $config[$param_or_index]
                        : $output[$param_or_index] = $default_or_param);
            }
        } else {
            array_key_exists($param, $config) 
                ? $output[$param] = $config[$param] 
                : $output[$param] = $default;
        }
        
        return is_array($param) ? $output : reset($output);
    }
    
    /**
    * Load given module config params
    * 
    * @param string $module
    */
    static private function _load($module) 
    {
        if (file_exists($file = APPLICATION_PATH . '/modules/' . $module . '/' . 
            self::CONFIG_FILENAME)) 
        {
            $config = new Zend_Config_Ini($file);
            self::$_cach[$module] = $config->toArray();
        }
    }
    
}


abstract class Config extends FinalView_Config
{ }