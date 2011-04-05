<?php
class User_PageController extends FinalView_Controller_Action
{
    
    public function createNewAction()
    {
        $form = new User_Form_Page;
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost() )) {
                $client = new Zend_Http_Client($form->getValue('uri'), array(
                    'timeout'      => 30
                ));

                try{
                    $response = $client->request();
                }catch(Zend_Http_Client_Adapter_Exception $e){
                    $form->uri->addError('ADDRESS_DOES_NOT_RESPOND');
                    return;
                }
                
                $page = Doctrine::getTable('Page')->create();
                $page->url = $form->getValue('uri');
                $page->populateFromResponse($response);
                $page->save();

                $this->_helper->redirector->gotoUrl(
                    $this->_helper->contentUrl('public', array('page_id' => $page->id), 'UserPageSetUp')
                );
            }
        }
    }
    
    public function setUpAction()
    {
        $page = Doctrine::getTable('Page')->findOneByParams(array(
            'id'    =>  $this->_getParam('page_id')
        ));
        
        $this->_helper->layout()->disableLayout();

        $vars = array(
            'contentDiv'    =>  md5(time() . 'content_div'),
            'visibilityDiv' =>  md5(time() . 'visibility_div'),
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
            'frame_url'     =>  $this->_helper->contentUrl('public', array(), 'UserPageFramePump')
        );
        
        $page->modifyContent(array(
            'cover_body_in_div' => array(
                'id'    =>  $vars['visibilityDiv'],
                'style' =>  array(
                    'visibility'  =>  'hidden',
                )
            )
        ));
        
        $page->modifyContent(array(
            'cover_body_in_div' => array(
                'id'    =>  $vars['contentDiv'],
                'style' =>  array(
                    'position'  =>  'relative',
                    'z-index'   =>  '1',
                )
            )
        ));
        
        $page->modifyDOM(array(
            'inject_scripts'    =>  array(
                'jquery.min.js',
                'jquery-ui.min.js',
                'lib.js'
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
                'embed.js',
                'overlap_flash.js',
                'overlap_frame.js'
            ),
            'change_iframe_src' =>  array(
                'url'   =>  $this->_helper->contentUrl('public', array(), 'UserPageFramePump')
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
                'id'    =>  'content_div',
                'style' =>  array(
                    'position'  =>  'absolute',
                    'z-index'   =>  '1',
                    'width'     =>  $params['document_width'] . 'px !important',
                    'left'      =>  -$params['left_width'] . 'px',
                    'top'       =>  -$params['top_height'] . 'px',
                )
            ),
        ));
        $crop_height = $params['bottom_top'] - $params['top_height'];
        
        $page->modifyContent(array(
            'cover_body_in_div' => array(
                'id'    =>  'crop_div',
                'style' =>  array(
                    'position'  =>  'relative',
                    'z-index'   =>  '1',
                    'display'   =>  'none',
                    'width'     =>  $params['top_width'] . 'px !important',
                    'height'    =>  $crop_height . 'px',
                    'overflow'  =>  'hidden'
                )
            ),
        ));
        
        $page->modifyDOM(array(
            'inject_scripts'    =>  array(
                'jquery.min.js',
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
        
        $page->saveToFile();

        $this->view->content = $page->Content->contents;
    }
    
    public function framePumpAction()
    {
        $this->_helper->layout()->disableLayout();
        $client = new Zend_Http_Client($this->_getParam('url'), array(
            'timeout'      => 30
        ));

        $response = $client->request();
        $page = Doctrine::getTable('Page')->create();
        $page->url = $this->_getParam('url');
        $page->populateFromResponse($response);
        
        $page->modifyDOM(array(
            'overlap_embed_objects',
            'inject_scripts'    =>  array(
                'jquery.min.js',
                'overlap_flash.js'
            ),
        ));
        
        $this->view->content = $page->Content->contents;
    }
}