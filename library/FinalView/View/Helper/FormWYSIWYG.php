<?php

class FinalView_View_Helper_FormWYSIWYG extends Zend_View_Helper_FormTextarea
{

	/**
	 * Generates a 'wysiwyg-textarea' element.
	 *
	 * @param string|array $name
	 * @param mixed $value
	 * @param array $attribs
	 * @return string
	 */
	public function formWYSIWYG($name, $value = null, $attribs = null)
	{
		$this->view->headScript()
			->appendFile('scripts/tiny_mce/tiny_mce.js')
			->appendFile('scripts/tiny_mce/init.js')
			->appendScript('init("wysiwyg");')
			;
		
		return $this->formTextarea($name, $value, (array)$attribs + array('class' => 'wysiwyg'));
	}

}