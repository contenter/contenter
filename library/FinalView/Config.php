<?php

/**
* This is fucking hell!
* 
* @example "#2 "QUOTE"Priority Listing"QUOTE"" -> #2 "Priority Listing"
*/
define('QUOTE', '"');

abstract class FinalView_Config 
{
    
    const CONFIG_FILENAME_INI  = 'config.ini';
    const CONFIG_FILENAME_YAML = 'config.yml';
    
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
            self::CONFIG_FILENAME_INI)) 
        {
            $config = new Zend_Config_Ini($file);
            self::$_cach[$module] = $config->toArray();
        }elseif (file_exists($file = APPLICATION_PATH . '/modules/' . $module . '/' . 
            self::CONFIG_FILENAME_YAML)) 
        {
            $config = Doctrine_Parser::load($file, 'yml');
            self::$_cach[$module] = $config;
        }
    }
    
    static public function factory($config = array(), $section= null, $options = false){
        if (is_string($config) and file_exists($config)) {
            $suffix      = strtolower(pathinfo($config, PATHINFO_EXTENSION));

            switch ($suffix){
                case "ini":
                    $configObj = new Zend_Config_Ini($config, $section, $options);
                    break;
                case "xml":
                    $configObj = new Zend_Config_Xml($config, $section, $options);
                    break;
                case "json":
                    $configObj = new Zend_Config_Json($config, $section, $options);
                    break;
                case "yaml":
                    $configObj = new Zend_Config_Yaml($config, $section, $options);
                    break;
                case "php":
                case "inc":
                    $configArr = include $config;
                    if (!is_array($config)) {
                        throw new Zend_Config_Exception('Invalid configuration file provided; PHP file does not return array value');
                    }
                    $configObj = new Zend_Config($configArr, $options);
                    break;
                default:
                    throw new Zend_Config_Exception("Invalid configuration file provided; unknown config type");
                    break;
            }
        }elseif(is_array($config)){
            $configObj = new Zend_Config($config, $options);
        }else {
            throw new Zend_Config_Exception("Invalid configuration file provided; unknown config type");
        }
        return $configObj;
    }
    
}


abstract class Config extends FinalView_Config
{ }
