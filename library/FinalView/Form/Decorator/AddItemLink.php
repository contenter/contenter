<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_Abstract */
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Zend_Form_Decorator_Fieldset
 *
 * Any options passed will be used as HTML attributes of the fieldset tag.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Fieldset.php 21966 2010-04-21 23:14:30Z alab $
 */
class FinalView_Form_Decorator_AddItemLink extends Zend_Form_Decorator_Abstract
{
    /**
     * Render link to add item
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }
        
        $view->headScript()->appendFile('/scripts/jquery-templates/jquery-tmpl-min.js');
        $view->headScript()->appendFile('/scripts/dynamic-items.js');
        $f = new Zend_Form_SubForm(array(
            'name'      =>  $element->getName(),
        ));
        $f->addSubForm(clone($element->getItemTemplate()), '__TEMPLATE__');
        $f->removeDecorator('HtmlTag');
        $f->removeDecorator('Fieldset');
        $f->removeDecorator('DtDdWrapper');

        $code = str_replace('__TEMPLATE__', '${__TEMPLATE}', $f->__toString());
        
        $link = '<a data-name="' . $element->getName() . '" data-itemsCount="' . $element->getItemsCount() . '" class="__ADD_ITEM_LINK__' . '" href="#">' . $view->escape($element->getAddLinkText()) . '</a>';
        $template =
        '<script id="' . $element->getName() . '-__TEMPLATE__" type="text/x-jquery-tmpl">
              ' . $code . '
        </script>';
        
        return $template . $content . $link;
    }
    
    private function _escapeJavaScript($string)
    {
        return strtr($string,
            array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n',
                '<'=>'\\074','>'=>'\\076','&'=>'\\046','--'=>'\\055\\055'));
    }
}
