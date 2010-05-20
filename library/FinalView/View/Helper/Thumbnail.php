<?php
class FinalView_View_Helper_Thumbnail extends Zend_View_Helper_HtmlElement
{
	private $_width = null;
	private $_height = null;
	private $_src = null;
	private $_imgPath = null;
	
	public function thumbnail($url, $width, $height, $attribs=false)
	{
		$this->_src = $url;
		$this->_width = $width;
		$this->_height = $height;

		$pathinfo = pathinfo($url);

		if (empty($pathinfo['filename']))
			return '';

		$this->_src = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
		
		$this->_imgPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->_src;
		if (!is_file($this->_src)) {
			$umask = umask(0);
			@mkdir($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $pathinfo['dirname'], 0777, true);
			umask($umask);

            require_once LIBRARY_PATH . '/asido/class.asido.php';
            Asido::driver('gd');

            $image = Asido::image($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $url, $this->_imgPath);
            Asido::frame($image, $width, $height);
            $image->save(ASIDO_OVERWRITE_ENABLED);

		}

		$attribs = array_merge(
			array(
				'src' => $this->_src,
				'width' => $this->_width,
				'height' => $this->_height
			)
		);

		$attribs = ($attribs) ? $this->_htmlAttribs($attribs) : '';
		$tag = 'img';
		return '<' . $tag . $attribs . $this->getClosingBracket(). PHP_EOL;
	}
}
?>
