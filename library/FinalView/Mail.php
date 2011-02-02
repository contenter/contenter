<?php

/**
 * Parse and send mail templates
 *
 * @author dV
 */
class FinalView_Mail
{

    const DEFAULT_CHARSET = 'utf-8';

    /**
    * Zend_Mail
    *
    * @var Zend_Mail
    */
    protected $_mailer;

    /**
    * Template
    *
    * @var FinalView_Mail_Template_Interface
    */
    protected $_template;

    /**
    * Vars to parse in template
    *
    * @var array
    */
    protected $_vars = array();

    public function __construct(FinalView_Mail_Template_Interface $template,
        array $vars = array(), $charset = self::DEFAULT_CHARSET)
    {
        $this->_mailer = new Zend_Mail($charset);
        $this->_mailer->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

        $this->_template = $template;
        $this->_vars = $vars;
    }

    /**
    * Set template vars
    *
    * @param array $vars
    */
    public function setVars(array $vars)
    {
        $this->_vars = $vars;
        return $this;
    }

    /**
    * Set default vars
    *
    */
    protected function _getDefaultVars()
    {
        $vars = array();

        if (defined('BASE_PATH')) {
            $vars['BASE_PATH'] = BASE_PATH;
        }

        return $vars;
    }

    /**
     * Magic method for calling Zend_Mail methods
     *
     * @param string $method_name
     * @param array $arguments
     * @return mixed
     */
    public function __call($method_name, $arguments)
    {
        return call_user_func_array(array($this->_mailer, $method_name), $arguments);
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

        $this->setSubject($this->_parse($this->_template->getSubject()));
        $this->setBodyText($this->_parse($this->_template->getBodyText()));
        $this->setBodyHtml($this->_parse($this->_template->getBodyHtml()));
        $this->_mailer->send();
    }

    /**
    * Parse vars in text
    *
    * @param string $string
    * @return string
    */
    private function _parse($string)
    {
        $vars = $this->_vars + $this->_getDefaultVars();

        return strtr($string, array_combine(
                array_map
                (
                    function ($var) { return '{$' . $var . '}'; },
                    array_keys($vars)
                ),
                $vars
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
        $dom = new DOMDocument(null, $this->_mailer->getCharset());
        if (@$dom->loadHTML($html)) {

            // "multipart/related" content type should be set in case any attachment was added
            $anyAttachmentAdded = false;

            // attache images
            $images = $dom->getElementsByTagName('img');
            for ($i = 0; $i < $images->length; $i++) {
                $item = $images->item($i);
                $src = $item->getAttribute('src');
                if ($mimeType = $this->_getMimeType($src)) {
                    $anyAttachmentAdded = true;
                    $contentID = $this->_addAttachment($src, file_get_contents($src), $mimeType);
                    // replace image src with attachment cid to display
                    // as inline attachment and not by the url
                    $item->setAttribute('src', 'cid:' . $contentID);
                }
            }

            // attache other linked files
            $links = $dom->getElementsByTagName('a');
            for ($i = 0; $i < $links->length; $i++) {
                $item = $links->item($i);
                $href = $item->getAttribute('href');
                if ($mimeType = $this->_getMimeType($href)) {
                    $anyAttachmentAdded = true;
                    $contentID = $this->_addAttachment($href, file_get_contents($href), $mimeType);
                    // also replace attachment link with cid to open it from attchment
                    $item->setAttribute('href', 'cid:' . $contentID);
                }
            }

            if ($anyAttachmentAdded) {
                $this->_mailer->setType(Zend_Mime::MULTIPART_RELATED);
            }

            $html = $dom->saveHTML();
        }



        return $this->_mailer->setBodyHtml($html, $charset, $encoding);
    }

    protected function _getMimeType($filename)
    {
        switch(true) {
            case in_array(parse_url($filename, PHP_URL_SCHEME), array('http', 'https')) :
                $definer = new FinalView_Mail_MimeType_ViaHttp;
                $mimeType = $definer->defineMimeType($filename);
                break;

            case file_exists($filename) :
                $definer = new FinalView_Mail_MimeType_ViaFS;
                $mimeType = $definer->defineMimeType($filename);
                break;

            default :
                $mimeType = null;
                break;
        }

        return $mimeType;
    }

    /**
     * Add attachemnt and return content id
     *
     * @param string $filename
     * @param string $content
     * @param string $mimeType
     * @return string
     */
    protected function _addAttachment($filename, $content, $mimeType)
    {
        $attachment = new Zend_Mime_Part($content);
        $attachment->id          = md5_file($filename);
        $attachment->filename    = pathinfo($filename, PATHINFO_BASENAME);

        $attachment->type        = $mimeType;
        $attachment->disposition = Zend_Mime::DISPOSITION_INLINE;
        $attachment->encoding    = Zend_Mime::ENCODING_BASE64;

        $this->_mailer->addAttachment($attachment);

        return $attachment->id;
    }

}
