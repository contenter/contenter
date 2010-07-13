<?php

/**
 * @link http://www.addthis.com/features
 *
 */
class FinalView_View_Helper_AddThisWidget extends Zend_View_Helper_Abstract
{

	const WIDGET =
		'<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style">
		<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=%s" class="addthis_button_compact">Share</a>
		<span class="addthis_separator">|</span>
		<a class="addthis_button_facebook"></a>
		<a class="addthis_button_email"></a>
		<a class="addthis_button_favorites"></a>
		<a class="addthis_button_print"></a>
		</div>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=%s"></script>
		<!-- AddThis Button END -->
		';

	public function addThisWidget($username = null)
	{
		return sprintf(self::WIDGET, $username, $username);
	}

}