<?php

class FinalView_Mail implements FinalView_Mail_Interface 
{
    
    /**
    * Zend_Mail
    * 
    * @var Zend_Mail
    */
    protected $_mailer;
    
    /**
    * Template name
    * 
    * @var string
    */
    private $_template;
    
    /**
    * Vars to parse in template
    * 
    * @var array
    */
    private $_vars = array();
    
    /**
    * Db model
    * 
    * @var string
    */
    private $_model = 'MailTemplates';
    
    /**
     * Mail character set
     * @var string
     */
    private $_charset = 'utf-8';
    
    
    public function __construct($template = null, array $vars = array(), 
        $model = null) 
    {
        $this->_mailer = new Zend_Mail($this->_charset);
        $this->_mailer->setFrom('no-reply@' . $_SERVER['HTTP_HOST']);
        $this->_mailer->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        
        if (!is_null($template)) {
            $this->_template = $template;
        }
        
        if (!empty($vars)) {
            $this->_vars = $vars;
        }
        $this->_setVarsDefault();
        
        if (!is_null($model)) {
            $this->_model = $model;
        }
    }
    
    /**
    * Set template
    * 
    * @param string $template
    */
    public function setTemplate($template) 
    {
        $this->_template = $template;
    }
    
    /**
    * Set template vars
    * 
    * @param array $vars
    */
    public function setVars(array $vars) 
    {
        $this->_vars = $vars;
    }
    
    /**
    * Set default vars set
    * 
    */
    protected function _setVarsDefault() 
    {
        if (defined('BASE_PATH')) {
            $this->_vars['BASE_PATH'] = BASE_PATH;
        }
    }
    
    /**
    * Set Mail character charset
    * 
    * @param string $charset
    */
    public function setCharset($charset) 
    {
        $this->_charset = $charset;
    }
    
    /**
    * Set model
    * 
    * @param string $model
    */
    public function setModel($model) 
    {
        $this->_model = $model;
    }
    
    /**
     * Magic method for calling Zend_Mail methods
     * 
     * @param string $method_name
     * @param array $arguments
     */
    public function __call($method_name, $arguments)
    {
        call_user_func_array(array($this->_mailer, $method_name), $arguments);
    }
    
    /**
    * Send email
    * 
    * @param string|array $email
    * @param string $name
    */
    public function send($email = null, $name = '') 
    {
        if (!is_null($email)) {
            if (is_array($email)) {
                foreach ($email as $_email => $_name) {
                    is_string($_email) 
                        ? $this->_mailer->addTo($_email, $_name)
                        : $this->_mailer->addTo($_name);
                }
            } else {
                $this->_mailer->addTo($email, $name);
            }
        }
        
        $this->_mailer->setSubject($this->_parse($this->_template()->subject));
        
        trim($this->_template()->html) 
            ? $this->setBodyHtml($this->_parse($this->_template()->html))
            : $this->_mailer->setBodyText($this->_parse($this->_template()->text));
        $this->_mailer->send();
    }
    
    /**
    * Return template
    * 
    * @return Doctrine_Record
    */
    protected function _template() 
    {
        static $cach = array();
        
        if (!array_key_exists($this->_template, $cach)) {
            
            $table = Doctrine::getTable($this->_model);
            $find = method_exists($table, 'findTemplate') ? 'findTemplate' : 'find';
            
            if (!$cach[$this->_template] = $table->{$find}($this->_template)) 
            {
                trigger_error('Template named "' . $this->_template . '" was not 
                    found', E_USER_ERROR);
            }
            
        }
        
        return $cach[$this->_template];
    }
    
    /**
    * Parse vars in text
    * 
    * @param string $string
    * @return string
    */
    private function _parse($string) 
    {
        return strtr($string, array_combine(
                array_map
                (
                    create_function('$var', 'return \'{$\' . $var . \'}\';'), 
                    array_keys($this->_vars)
                ), 
                $this->_vars
            ));
    }
    
    
    /**
     * Sets the HTML body for the message
     *
     * @param  string    $html
     * @param  string    $charset
     * @param  string    $encoding
     * @return Zend_Mail Provides fluent interface
     */
    public function setBodyHtml($html, $charset = null, 
        $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_mailer->setType(Zend_Mime::MULTIPART_RELATED);

        $dom = new DOMDocument(null, $this->_mailer->getCharset());
        @$dom->loadHTML($html);

        $images = $dom->getElementsByTagName('img');
        
        for ($i = 0; $i < $images->length; $i++) {
            $url = $images->item($i)->getAttribute('src');
            
            $image_http = new Zend_Http_Client($url);
            $response = $image_http->request(Zend_Http_Client::GET);
            
            if (200 == $response->getStatus())
            {
                $mime = new Zend_Mime_Part($response->getBody());
                $mime->id          = $url;
                $mime->location    = $url;
                $mime->type        = $response->getHeader(Zend_Http_Client::CONTENT_TYPE);
                $mime->disposition = Zend_Mime::DISPOSITION_INLINE;
                $mime->encoding    = Zend_Mime::ENCODING_BASE64;
                $mime->filename    = pathinfo($url, PATHINFO_BASENAME);

                $this->_mailer->addAttachment($mime);
            }
        }
        
        return $this->_mailer->setBodyHtml($html, $charset, $encoding);
    }
    
}