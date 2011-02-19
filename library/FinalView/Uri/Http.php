<?php
class FinalView_Uri_Http extends Zend_Uri_Http
{
    /**
     * Returns a URI based on current values of the instance variables. If any
     * part of the URI does not pass validation, then an exception is thrown.
     *
     * @throws Zend_Uri_Exception When one or more parts of the URI are invalid
     * @return string
     */
    public function getBaseUri()
    {
        if ($this->valid() === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception('One or more parts of the URI are invalid');
        }

        $password = strlen($this->_password) > 0 ? ":$this->_password" : '';
        $auth     = strlen($this->_username) > 0 ? "$this->_username$password@" : '';
        $port     = strlen($this->_port) > 0 ? ":$this->_port" : '';

        return $this->_scheme
             . '://'
             . $auth
             . $this->_host
             . $port;
    }
    
    /**
     * Creates a Zend_Uri_Http from the given string
     *
     * @param  string $uri String to create URI from, must start with
     *                     'http://' or 'https://'
     * @throws InvalidArgumentException  When the given $uri is not a string or
     *                                   does not start with http:// or https://
     * @throws Zend_Uri_Exception        When the given $uri is invalid
     * @return Zend_Uri_Http
     */
    public static function fromString($uri)
    {
        if (is_string($uri) === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception('$uri is not a string');
        }

        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (in_array($scheme, array('http', 'https')) === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception("Invalid scheme: '$scheme'");
        }

        $schemeHandler = new FinalView_Uri_Http($scheme, $schemeSpecific);
        return $schemeHandler;
    }
}