<?php

/**
 * Output specified file
 *
 * @author dV
 */
class FinalView_Controller_Action_Helper_FileTransferHttp
    extends Zend_Controller_Action_Helper_Abstract
{

    const DEFAULT_MIME_TYPE = 'application/octet-stream';

    public function send($file)
    {
        if (!Zend_Loader::isReadable($file)) {
            trigger_error('Given file is not readable', E_USER_ERROR);
        }

        $this->_disableLayout();

        $this->getResponse()
            ->setHeader('Content-Type', $this->_defineMimeType($file))
            ->setHeader('Content-Disposition', 'attachment; filename="' . pathinfo($file, PATHINFO_BASENAME) . '"')
            ->setHeader('Content-Length', filesize($file))
            ;

        readfile($file);
    }

    private function _disableLayout()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
    }

    private function _defineMimeType($file)
    {
        $mime_type = self::DEFAULT_MIME_TYPE;

        $mimefile = defined('MAGIC') ? constant('MAGIC') : '';
        if (class_exists('finfo', false)) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            if (!empty($mimefile)) {
                $mime = new finfo($const, $mimefile);
            } else {
                $mime = new finfo($const);
            }

            if ($mime !== false) {
                $mime_type = $mime->file($file);
            }
            unset($mime);
        }

        if (empty($mime_type) &&
            (function_exists('mime_content_type') && ini_get('mime_magic.magicfile'))) {
                $mime_type = mime_content_type($file);
        }

        return $mime_type;
    }

}