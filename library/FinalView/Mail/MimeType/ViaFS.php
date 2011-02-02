<?php

class FinalView_Mail_MimeType_ViaFS implements FinalView_Mail_MimeType_Interface
{

    public function defineMimeType($filename)
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
                $mime_type = $mime->file($filename);
            }
            unset($mime);
        }

        if (empty($mime_type) &&
            (function_exists('mime_content_type') && ini_get('mime_magic.magicfile'))) {
                $mime_type = mime_content_type($filename);
        }

        return $mime_type;
    }

}