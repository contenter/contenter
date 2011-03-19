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
                
                if ($form->isValidResponse($response) ) {
                    $page = Doctrine::getTable('Page')->create();
                    $page->url = $form->getValue('uri');
                    $page->populateFromResponse($response);
                    $page->save();
                }

                $this->_helper->redirector->gotoUrl(
                    $this->_helper->contentUrl('public', array('page_id' => $page->id), 'UserPageSetUp')
                );
            }
        }
        
        $this->view->form = $form;
    }
    
    public function setUpAction()
    {
        $page = Doctrine::getTable('Page')->findOneByParams(array(
            'id'    =>  $this->_getParam('page_id')
        ));
        
        $this->_helper->layout()->disableLayout();

        $vars = array(
            'contentDiv'    =>  md5(time() . 'content_div'),
            'ld'            =>  md5(time() . 'left_div'),
            'rd'            =>  md5(time() . 'right_div'),
            'td'            =>  md5(time() . 'top_div'),
            'bd'            =>  md5(time() . 'bottom_div'),
            'transd'        =>  md5(time() . 'transparent_div'),
            'lwi'           =>  md5(time() . 'left_width'),
            'thi'           =>  md5(time() . 'top_height'),
            'twi'           =>  md5(time() . 'top_width'),
            'bti'           =>  md5(time() . 'bottom_top'),
            'fd'            =>  md5(time() . 'form_div'),
            'documentW'     =>  md5(time() . 'document_width'),
            'documentH'     =>  md5(time() . 'document_height'),
        );
        
        $page->modifyContent(array(
            'cover_body_in_div' => array(
                'id'    =>  $vars['contentDiv'],
                'style' =>  array(
                    'position'  =>  'relative',
                    'z-index'   =>  '1'
                )
            )
        ));
        
        $page->modifyDOM(array(
            'inject_scripts'    =>  array(
                'jquery.min.js',
                'jquery-ui.min.js'
            ),
            'inject_css'        =>  array(
                'jquery-ui.css'
            ),
            'inject_overlap_structure'  =>  $vars,
            'inject_feedback_form'      =>  $vars + array(
                'form_action' => $this->_helper->contentUrl('public', array('page_id'    =>  $page->id), 'UserPageBuild')
            ),
            'overlap_embed_objects',
        ));
        
        $page->modifyDOM(array(
            'assign_js_variables'   =>  $vars,
            'inject_scripts'    =>  array(
                'embed.js'
            )
        ));
        
        $this->view->content = $page->Content->contents;
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
            'document_width',
            'document_height',
        );
        
        $params = array();

        foreach ($this->getRequest()->getPost() as $key => $paramValue ) {
            foreach ($allowedParams as $newKey) {
                if (strpos($key, $newKey) !== false) {
                    $params[$newKey] = trim($paramValue, 'px');
                }
            }
        }
        
        $page->modifyContent(array(
            'cover_body_in_div' => array(
                'id'    =>  'body_div',
                'style' =>  array(
                    'position'  =>  'relative',
                    'z-index'   =>  '1',
                    'display'   =>  'none',
                    'width'     =>  $params['document_width'] . 'px !important'
                )
            ),
        ));
        
        
        $frameStyle = array(
            'position'          =>  'absolute',
            'background-color'  =>  'white',
            'z-index'           =>  '3'
        );
        
        $TOP = 40;
        $fl_and_fr_height = $params['bottom_top'] - $params['top_height'];
        $fb_height = $params['document_height'] - $params['bottom_top'];
        $fb_top = $TOP + $fl_and_fr_height;

        $ftStyle = $frameStyle + array('left' => '0px', 'top' => '0px',
            'width' => '100%', 'height' => $TOP . 'px');
            
        $flStyle = $frameStyle + array('left' => '0px', 'top' => $TOP . 'px',
            'width' => '0px', 'height' => $fl_and_fr_height . 'px');
            
        $frStyle = $frameStyle + array('left' => $params['top_width'] . 'px', 'top' => $TOP . 'px',
            'width' => '0px', 'height' => $fl_and_fr_height . 'px');
            
        $fbStyle = $frameStyle + array('left' => '0px', 'top' => $fb_top . 'px',
            'width' => '100%', 'height' => $fb_height . 'px');

        $page->modifyDOM(array(
            'inject_scripts'    =>  array(
                'jquery.min.js',
            ),
            'inject_iframes' =>  array(
                array(
                    'id'            =>  'fl',
                    'frameborder'   =>  0,
                    'style'         =>  $flStyle
                ),
                array(
                    'id'            =>  'ft',
                    'frameborder'   =>  0,
                    'style'         =>  $ftStyle
                ),
                array(
                    'id'            =>  'fb',
                    'frameborder'   =>  0,
                    'style'         =>  $fbStyle
                ),
                array(
                    'id'            =>  'fr',
                    'frameborder'   =>  0,
                    'style'         =>  $frStyle
                )
            ),
        ));
        
        $page->modifyDOM(array(
            'assign_js_variables'   =>  array(
                'top_width'     =>  $params['top_width'],
                'left_width'    =>  $params['left_width'],
                'top_height'    =>  $params['top_height'],
            ),
            'inject_scripts'    =>  array(
                'build.js',
            ),
        ));
        
//         $page->Content->built_content = $page->Content->contents;
//         $page->save();

        $this->view->content = $page->Content->contents;
    }
}