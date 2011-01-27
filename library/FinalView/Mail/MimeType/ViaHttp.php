<?php

class FinalView_Mail_MimeType_ViaHttp implements FinalView_Mail_MimeType_Interface
{

    public function defineMimeType($url)
    {
        $httpClient = new Zend_Http_Client($url);
        $response = $httpClient->request(Zend_Http_Client::GET);

        return 200 == $response->getStatus() 
            ? $response->getHeader(Zend_Http_Client::CONTENT_TYPE)
            : self::DEFAULT_MIME_TYPE;
    }

}