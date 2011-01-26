<?php

interface FinalView_Mail_MimeType_Interface
{

    const DEFAULT_MIME_TYPE = 'application/octet-stream';

    public function defineMimeType($filename);

}