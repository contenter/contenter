<?php

/**
* Validate whether given file is a sort of video file. 
* 
* Mime-type validation based on ,i,e-magic doesn't work properly: 
* "application/octet-stream" instead "video/..."
* 
*/
class FinalView_Validate_File_IsVideo extends Zend_Validate_Abstract 
{
    
    /**#@+
     * @const Error type constants
     */
    const NOT_VIDEO = 'NotVideoFile';
    /**#@-*/
    
    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array
    (
        self::NOT_VIDEO => 'Uploaded file is not a sort of video file.', 
    );
    
    public function __construct()    
    {
        // first check whether we can work with video
        if (!extension_loaded('ffmpeg')) {
            trigger_error('ffmpeg extenstion is not loaded', E_USER_ERROR);
        }
        if (!class_exists('ffmpeg_movie', false)) {
            trigger_error('Class "ffmpeg_movie" does not exist', E_USER_ERROR);
        }
    }
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if the mimetype of the file matches the given ones. Also parts
     * of mimetypes can be checked. If you give for example "image" all image
     * mime types will be accepted like "image/gif", "image/jpeg" and so on.
     *
     * @param  string $value Real file to check for mimetype
     * @param  array  $file  File data from Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null) 
    {
        // Try to init object
        @$ffmpeg_movie = new ffmpeg_movie($value);
        if ($ffmpeg_movie && $ffmpeg_movie->getFrameCount() > 0) {
            return true;
        }
        
        $this->_error(self::NOT_VIDEO);
        return false;
    }
    
}
