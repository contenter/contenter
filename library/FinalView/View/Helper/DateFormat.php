<?php

class FinalView_View_Helper_DateFormat extends Zend_View_Helper_Abstract
{
    
    /**
    * Date format
    * 
    * @var string
    */
    private static $_format;
    
    /**
    * Set date format
    * 
    * @param string $format
    */
    static public function setFormat($format) 
    {
        self::$_format = $format;
    }
    
    /**
    * Time zone name
    * 
    * @var string
    */
    private static $_time_zone;
    
    /**
    * Set time zone
    * 
    * @param string $time_zone
    */
    static public function setTimeZone($time_zone) 
    {
        self::$_time_zone = $time_zone;
    }
    
    /**
    * Format date according to the given format or previously set format. 
    * 
    * @param string $date
    * @param mixed $format
    * @return string
    */
    public function dateFormat($date, $format = null, $time_zone = null) 
    {
        $time_zone = is_null($time_zone) ? self::$_time_zone : $time_zone;
        $locale = Zend_Registry::isRegistered('Zend_Locale') ? Zend_Registry::get('Zend_Locale') : null;
        
        $date = new Zend_Date($date, Zend_Date::ISO_8601, $locale);
        if (!is_null($time_zone)) {
            $date->setTimezone($time_zone);
        }
        return $date->toString(!is_null($format) ? $format : self::$_format);
    }
    
}