<?php

/**
 * @link http://www.addthis.com/features
 * @author dV
 */
class FinalView_View_Helper_AddThisWidget extends Zend_View_Helper_Abstract
{

    const WIDGET =
        '<!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style">
        <a href="http://www.addthis.com/bookmark.php?v=250&amp;username=%s" class="addthis_button_compact">Share</a>
        <span class="addthis_separator">|</span>
        <a class="addthis_button_facebook" title="Send to Facebook"></a>
        <a class="addthis_button_email" title="Email"></a>
        <a class="addthis_button_favorites" title="Save to Favorites"></a>
        <a class="addthis_button_print" title="Print"></a>
        </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=%s"></script>
        <!-- AddThis Button END -->
        ';

    public function addThisWidget($username = null, $url = null)
    {

        return  sprintf(self::WIDGET, $username,  $username);
    }

}