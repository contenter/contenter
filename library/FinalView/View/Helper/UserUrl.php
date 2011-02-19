<?php

class FinalView_View_Helper_UserUrl extends Zend_View_Helper_Url
{
    
    /**
    * Logged user id
    * 
    * @var integer
    */
    static private $_logged_user_id;
    
    /**
    * Name of the user ID key in $urlOptions array
    * 
    * @var string
    */
    static private $_user_id_key_name = 'user_id';
    
    /**
    * Set user id key name
    * 
    * @param string $user_id_key_name
    */
    static public function setUserIdKeyName($user_id_key_name) 
    {
        self::$_user_id_key_name = $_user_id_key_name;
    }
    
    /**
    * Set logged user id
    * 
    * @param integer $logged_user_id
    */
    static public function setLoggedUserId($logged_user_id) 
    {
        self::$_logged_user_id = $logged_user_id;
    }
    
    /**
     * Generates an url given the name of a route.
     * Exclude user_id param from $urlOptions in case it matches logged user id.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function userUrl(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        if (is_null($name)) {
            $name = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        }

        if ('ProfileView' == $name) {
            if (!array_key_exists(self::$_user_id_key_name, $urlOptions)) {
                $urlOptions[self::$_user_id_key_name] = self::$_logged_user_id;
            }

            return $this->url($urlOptions, $name, $reset, $encode);
        }

        // fucking shirt !

        $query_string = '';
        
        if (array_key_exists(self::$_user_id_key_name, $urlOptions) && 
            $urlOptions[self::$_user_id_key_name] != self::$_logged_user_id) 
        {
            $query_string = '?' . http_build_query(array(
                self::$_user_id_key_name => $urlOptions[self::$_user_id_key_name]));
            unset($urlOptions[self::$_user_id_key_name]);
        }
        
        return $this->url($urlOptions, $name, $reset, $encode) . $query_string;
    }
    
}