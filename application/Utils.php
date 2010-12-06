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
    static public function getDir($dir, $system)
    {
        if ((bool)$system) {
            $dir = PUBLIC_PATH . DIRECTORY_SEPARATOR . $dir;
			if (!is_dir($dir)) {
				mkdir($dir, 0777, true);
                chgrp($filename, $group);
			}
        }

        return $dir;
    }

    /**
    * Delete given file from file system
    *
    * @param string $file
    */
    static public function removeFile($file)
    {
        if (file_exists($file) && is_file($file)) {
            return unlink($file);
        }
    }

	/**
     * @deprecated
     */
    static protected function _getDir($dir, $system)
    {
        return self::getDir($dir, $system);
    }

	/**
     * @deprecated
     */
    static protected function _removeFile($file)
    {
        self::removeFile($file);
    }

    static public function clearDir($dir)
	{
		$pattern = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '{,.}*';

		foreach (glob($pattern, GLOB_BRACE) as $filename) {
			if (in_array(basename($filename), array('.', '..'))) {
				continue;
			}

			if (is_dir($filename)) {
				self::clearDir($filename);
				@rmdir($filename);
			} else {
				@unlink($filename);
			}
		}
	}

}