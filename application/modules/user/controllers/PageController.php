<?php
class User_PageController extends FinalView_Controller_Action
{
    
    public function createNewAction()
    {
        $form = new User_Form_Page;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost() )) {

                $client = new Zend_Http_Client($form->getValue('uri'), array(
                    'timeout'      => 30
                ));

                $response = $client->request();
                

                
                if ($content_type = $response->getHeader('Content-Type')) {
                    preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $content_type, $matches );
                    if ( isset( $matches[1] ) )
                        $mime = $matches[1];
                    if ( isset( $matches[3] ) )
                        $charset = $matches[3];
                }
                
                $content = $response->getBody();
                
                if(!isset($charset)){
                    preg_match( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s+charset=([^\s"]+))?@i',
                        $content, $matches );
                    if ( isset( $matches[1] ) && !isset($mime) )
                        $mime = $matches[1];
                    if ( isset( $matches[3] ) )
                        $charset = $matches[3];
                }
                
                if ($charset && !in_array($charset, array('utf-8', 'utf8', 'UTF-8', 'UTF8', 'Utf8', 'Utf-8')) ) {
                    $content = mb_convert_encoding($content, 'UTF-8', $charset);
                }
                

                $tidy = new tidy;
                $config = array(
                    'drop-proprietary-attributes' => true,
                    'hide-comments' => true,
                    'indent' => true,
                    'logical-emphasis' => true,
                    'numeric-entities' => true,
                    'output-xhtml' => true,
                    'wrap' => 0
                );
                $tidy->parseString($content, $config, 'utf8');
                $tidy->cleanRepair();
                $content = $tidy->value;
                $content = preg_replace('#<meta[^>]+>#isu', '', $content);
                $content = preg_replace('#<head\b[^>]*>#isu', "<head>\r\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />", $content);

                $page = Doctrine::getTable('Page')->create(array(
                    'url'        =>  $form->getValue('uri'),
                    'contents'   =>  $content
                ) );

                $page->save();
                
                $this->_helper->redirector->gotoUrl(
                    $this->_helper->contentUrl('public', array('page_id' => $page->id), 'UserPageSetUp')
                );
            }
        }
        
        $this->view->form = $form;
    }
    
    private function _buildElement($elem, $document)
    {
        if (!isset($elem['tag'])) {
            trigger_error('_buildElement must get array with tag key');
        }

        $elemObj = $document->createElement($elem['tag']);
        foreach ((array)@$elem['attribs'] as $key   =>  $value) {
            $attrib = $document->createAttribute($key);
            $attribValue = $document->createTextNode($value);
            $attrib->appendChild($attribValue);
            $elemObj->appendChild($attrib);
        }
        
        if (is_array(@$elem['inner_content'])) {
            foreach ($elem['inner_content'] as $innerElement) {
                $elemObj->appendChild($this->_buildElement($innerElement, $document) );
            }
        }elseif (is_string(@$elem['inner_content']) ) {
            $innerContent = $document->createTextNode($elem['inner_content']);
            $elemObj->appendChild($innerContent);
        }
        
        return $elemObj;
    }
    
    public function setUpAction()
    {
        $page = Doctrine::getTable('Page')->findOneByParams(array(
            'id'    =>  $this->_getParam('page_id')
        ));
        
        $this->_helper->layout()->disableLayout();

        $document = new DOMDocument();
        $page_content = $page->contents;
        $page_content = preg_replace("/<body([^>]*)>/i", '<body$1><div style="position:relative;z-index:1;">', $page_content);
        $page_content = preg_replace("/<\/body>/i", '</div></body>', $page_content);

        @$document->loadHTML($page_content);

        $head = $document->getElementsByTagName('head')->item(0);
        if (!$head) {
            $head = $document->createElement('head');
            $document->getElementsByTagName('html')->item(0)->appendChild($head);
        }
        
        if ($document->getElementsByTagName('base')->length < 1) {

            $uri = FinalView_Uri_Http::fromString($page->url);

            $head->insertBefore($this->_buildElement(array(
                'tag'       =>  'base',
                'attribs'   =>  array(
                    'href'  =>  $uri->getBaseUri()
                )
            ), $document), $head->firstChild);
        }

        $body = $document->getElementsByTagName('body')->item(0);

        $script = array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'scripts/jquery.min.js',
                'type'  =>  'text/javascript'
            )
        );

        $body->appendChild($this->_buildElement($script, $document));
        
        $script = array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'scripts/jquery-ui.min.js',
                'type'  =>  'text/javascript'
            )
        );

        $body->appendChild($this->_buildElement($script, $document));

        $style = array(
            'tag'       =>  'link',
            'attribs'   =>  array(
                'href'      =>  BASE_PATH . 'scripts/jquery-ui.css',
                'rel'       =>  'stylesheet',
                'type'      =>  'text/css'
            )
        );

        $body->appendChild($this->_buildElement($style, $document));

        $ld_id = md5(time() . 'left_div');
        $rd_id = md5(time() . 'right_div');
        $td_id = md5(time() . 'top_div');
        $bd_id = md5(time() . 'bottom_div');
        
        $transparent_div_id = md5(time() . 'transparent_div');
        
        $cWidth = '100%';
        $cHeight = '1200px';
        $div_style = 'position:absolute;z-index:10;background-color:white;opacity: 0.8;filter: alpha(Opacity=80);left:0px;top:0px;';
        $transparent_div_style = 'position:absolute;z-index:5;background-color:white;
                                  opacity: 0;filter: alpha(Opacity=0);left:0px;top:0px;
                                  width:' . $cWidth . ';height:' . $cHeight . ';';

        $body->insertBefore($this->_buildElement(array(
            'tag'       =>  'div',
            'attribs'   =>  array(
                'style' =>  'position:relative;width:' . $cWidth . ';height' . $cHeight . ';z-index:10;'
            ),
            'inner_content' =>  array(
                array(
                    'tag'       =>  'div',
                    'attribs'   =>  array(
                        'id'    =>  $transparent_div_id,
                        'style' =>  $transparent_div_style
                    )
                ),
                array(
                    'tag'       =>  'div',
                    'attribs'   =>  array(
                        'id'    =>  $ld_id,
                        'style' =>  $div_style . 'width:0px;height:' . $cHeight . ';'
                    )
                ),
                array(
                    'tag'       =>  'div',
                    'attribs'   =>  array(
                        'id'    =>  $td_id,
                        'style' =>  $div_style . 'width:0px;height:0px;'
                    )
                ),
                array(
                    'tag'       =>  'div',
                    'attribs'   =>  array(
                        'id'    =>  $bd_id,
                        'style' =>  $div_style . 'width:0px;height:0px;'
                    )
                ),
                array(
                    'tag'       =>  'div',
                    'attribs'   =>  array(
                        'id'    =>  $rd_id,
                        'style' =>  $div_style . 'width:' . $cWidth . ';height:' . $cHeight . ';'
                    )
                ),
            )
        ), $document), $body->firstChild);

        $lw_id = md5(time() . 'left_width');
        $th_id = md5(time() . 'top_height');
        $tw_id = md5(time() . 'top_width');
        $bt_id = md5(time() . 'bottom_top');
        $fd_id = md5(time() . 'form_div');
        $bodyw_id = md5(time() . 'body_width');
        $bodyh_id = md5(time() . 'body_height');

        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'div',
            'attribs'   =>  array(
                'id'    =>  $fd_id,
                'style' =>  'position:absolute;display:none;z-index:20;'
            ),
            'inner_content' =>  array(
                array(
                    'tag'       =>  'form',
                    'attribs'   =>  array(
                        'action'    =>  $this->_helper->contentUrl(
                                'public', array('page_id'    =>  $page->id), 'UserPageBuild'),
                        'method'    =>  'post'
                    ),
                    'inner_content' =>  array(
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $lw_id,
                                'name'  =>  $lw_id . '_left_width'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $th_id,
                                'name'  =>  $th_id . '_top_height'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $tw_id,
                                'name'  =>  $tw_id . '_top_width'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $bt_id,
                                'name'  =>  $bt_id . '_bottom_top'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $bodyw_id,
                                'name'  =>  $bodyw_id . '_body_width'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'hidden',
                                'id'    =>  $bodyh_id,
                                'name'  =>  $bodyh_id . '_body_height'
                            )
                        ),
                        array(
                            'tag'   =>  'input',
                            'attribs'   =>  array(
                                'type'  =>  'button',
                                'value' =>  'Build Page'
                            )
                        ),
                    )
                )
            )
        ), $document));

        $body->insertBefore($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'header.html',
                'style' =>  'background-color:white;position:relative;width:100%;height:80px;z-index:11;'
            )
        ), $document), $body->firstChild);

        $xpath = new DOMXpath($document);

        $objects = $document->getElementsByTagName('object');

        foreach ($objects as $object) {
            $wmodeParam = $xpath->query('param[@name="wmode"]', $object);
            if ($wmodeParam->length < 1) {
             $object->appendChild($this->_buildElement(array(
                 'tag'       =>  'param',
                 'attribs'   =>  array(
                     'name'   =>  'wmode',
                     'value'  =>  'opaque'
                 )
             ), $document));
            }else{
                $wmodeParam->item(0)->setAttribute('value', 'opaque');
            }
        }

        $embeds = $document->getElementsByTagName('embed');
        foreach ($embeds as $embed) {
            $embed->setAttribute('wmode', 'opaque');
        }
        
        $jsArray = Zend_Json::encode(array(
            'ld'        =>  $ld_id,
            'rd'        =>  $rd_id,
            'td'        =>  $td_id,
            'bd'        =>  $bd_id,
            'transd'    =>  $transparent_div_id,
            'lwi'       =>  $lw_id,
            'thi'       =>  $th_id,
            'twi'       =>  $tw_id,
            'bti'       =>  $bt_id,
            'fd'        =>  $fd_id,
            'bodyw'     =>  $bodyw_id,
            'bodyh'     =>  $bodyh_id,
        ));

        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'type'  =>  'text/javascript'
            ),
            'inner_content' =>  '$(function(){$(document).data("divs", ' . $jsArray . ')} )'
        ), $document));

        $script = array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'scripts/embed.js',
                'type'  =>  'text/javascript'
            )
        );
        $body->appendChild($this->_buildElement($script, $document));

        $this->view->content = $document->saveHTML();
    }

    public function buildAction()
    {
        $page = Doctrine::getTable('Page')->findOneByParams(array(
            'id'    =>  $this->_getParam('page_id')
        ));

        $this->_helper->layout()->disableLayout();

        $allowedParams = array(
            'left_width',
            'top_height',
            'top_width',
            'bottom_top',
            'body_width',
            'body_height',
        );
        
        $params = array();

        foreach ($this->getRequest()->getPost() as $key => $paramValue ) {
            foreach ($allowedParams as $newKey) {
                if (strpos($key, $newKey) !== false) {
                    $params[$newKey] = $paramValue;
                }
            }
        }

//        dump($params);
//        exit;

        $document = new DOMDocument();
        $page_content = $page->contents;

        $needLeftPos = (int)(((int)trim($params['body_width'], 'px') - (int)trim($params['top_width'], 'px')) / 2);
        $lw = (int)trim($params['left_width'], 'px');
        $leftOffset = ($lw > $needLeftPos) ? -($lw - $needLeftPos) : ($needLeftPos - $lw);
        $topOffset = -(int)trim($params['top_height'], 'px') + 90;

        $page_content = preg_replace(
                "/<body([^>]*)>/i",
                '<body$1><div id="body_div" style="display:none;position:relative;z-index:1;">',
                $page_content
        );
        $page_content = preg_replace("/<\/body>/i", '</div></body>', $page_content);

        @$document->loadHTML($page_content);

        $head = $document->getElementsByTagName('head')->item(0);
        if (!$head) {
            $head = $document->createElement('head');
            $document->getElementsByTagName('html')->item(0)->appendChild($head);
        }

        if ($document->getElementsByTagName('base')->length < 1) {

            $uri = FinalView_Uri_Http::fromString($page->url);

            $head->insertBefore($this->_buildElement(array(
                'tag'       =>  'base',
                'attribs'   =>  array(
                    'href'  =>  $uri->getBaseUri()
                )
            ), $document), $head->firstChild);
        }

        $body = $document->getElementsByTagName('body')->item(0);

        $styleAttr = trim($body->getAttribute('style'), ';');

        if (!empty($styleAttr)) {
            $wset = false;
            foreach (explode(';', $styleAttr) as $style) {
                list($key, $value) = explode(':', $style);
                if ($key == 'width') {
                    $wset = true;
                    $value = $params['body_width'] . ' !important';
                }
                $css[] = $key . ': ' . $value;
            }
            if (!$wset) {
                $css[] = 'width: ' . $params['body_width'] . ' !important';
            }

            $body->setAttribute('style', implode(';', $css));
        }else{
            $body->setAttribute('style', 'width: ' . $params['body_width'] . ' !important');
        }

        $script = array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'scripts/jquery.min.js',
                'type'  =>  'text/javascript'
            )
        );

        $body->appendChild($this->_buildElement($script, $document));

        $body->insertBefore($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'header.html',
                'style' =>  'background-color:white;position:absolute;width:100%;height:80px;z-index:4;'
            )
        ), $document), $body->firstChild);

        $frameStyle = 'position:absolute;background-color:white;z-index: 3;';

        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'id'    =>  'fl',
                'frameborder'   =>  0,
                'style' =>  $frameStyle . 'left: 0px;top: 0px; width: 0px;height:' . $params['body_height'] . ';'
            )
        ), $document));
        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'id'    =>  'ft',
                'frameborder'   =>  0,
                'style' =>  $frameStyle . 'left: 0px;top: 0px; width: ' . $params['top_width'] . ';height: 90px;'
            )
        ), $document));
        $bt = 90 + ((int)trim($params['bottom_top'], 'px') - (int)trim($params['top_height'], 'px'));
        $fb_height = (int)trim($params['body_height'], 'px') - $bt;
        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'id'    =>  'fb',
                'frameborder'   =>  0,
                'style'         =>
                    $frameStyle . 'left: 0px;top: ' . $bt . 'px; width: ' . $params['top_width'] . ';height: ' . $fb_height . 'px;',
            )
        ), $document));

        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'iframe',
            'attribs'   =>  array(
                'id'    =>  'fr',
                'frameborder'   =>  0,
                'style'         =>
                    $frameStyle . 'left: 0px;top: 0px; width: 0px;height:' . $params['body_height'] . ';',
            )
        ), $document));
        
        $jsArray = Zend_Json::encode(array(
            'body_width'    =>  (int)trim($params['body_width'], 'px'),
            'top_width'     =>  (int)trim($params['top_width'], 'px'),
            'left_width'    =>  (int)trim($params['left_width'], 'px'),
            'top_height'    =>  (int)trim($params['top_height'], 'px'),
            'top_width'     =>  (int)trim($params['top_width'], 'px')
        ));

        $body->appendChild($this->_buildElement(array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'type'  =>  'text/javascript'
            ),
            'inner_content' =>  '$(function(){$(document).data("params", ' . $jsArray . ')} )'
        ), $document));

        $script = array(
            'tag'       =>  'script',
            'attribs'   =>  array(
                'src'   =>  BASE_PATH . 'scripts/build.js',
                'type'  =>  'text/javascript'
            )
        );
        $body->appendChild($this->_buildElement($script, $document));

        $this->view->content = $document->saveHTML();
    }
}