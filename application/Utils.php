<?php

abstract class Utils
{

	/**
    * Return path system or http. If system path does not exists create one.
    *
    * @param string $dir
    * @param boolean $system
    *
    * @return string
    */
    static protected function _getDir($dir, $system)
    {
        if ((bool)$system) {
            $dir = PUBLIC_PATH . DIRECTORY_SEPARATOR . $dir;
			if (!is_dir($dir)) {
				mkdir($dir);
				chmod($dir, 0777);
			}
        }

        return $dir;
    }

	/**
    * Delete given file from file system
    *
    * @param string $file
    */
    static protected function _removeFile($file)
    {
        if (file_exists($file) && is_file($file)) {
            unlink($file);
        }
    }

}