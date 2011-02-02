<?php

class ErrorController extends Zend_Controller_Action
{

    /**
     * Pre-dispatch routines
     *
     * Called before action method. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to skip processing the current action.
     *
     * @return void
     */
    public function preDispatch()
    {
        $error_handler = $this->_getParam('error_handler');

        $this->view->exception = $error_handler->exception;
        $this->view->request   = $error_handler->request;
    }

    public function errorAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();

        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = $errors->exception->getMessage();
            break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                switch ($errors->exception->getCode()) {
                    case 403:
                    case 404:
                        $this->getResponse()->setHttpResponseCode($errors->exception->getCode());
                    break;
                    default:
                        $this->getResponse()->setHttpResponseCode(500);
                    break;
                }
                $this->view->message = $errors->exception->getMessage();
            break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
            break;
        }


        if ($this->getRequest() instanceof FinalView_Controller_Request_Cli) {
            echo $errors->exception . "\n\n";
        } else {
            $this->view->exception = $errors->exception;
            $this->view->request   = $errors->request;
        }
    }

    public function ajaxErrorAction()
    {

        $this->view->assign(array(
            'errors'     => array(),
            'errorCode' => 1
        ));

        FinalView_View_Helper_Translate::getInstance()->importModule('customization');

        foreach ( $this->_getParam('errors') as $e ) {
            if (is_array($e)) { $this->view->errors[] = __($e[0], $e[1]);}
            else { $this->view->errors[] = __($e); }
        }

        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getResponse()->setHttpResponseCode($this->_getParam('responseCode'));
        $this->_helper->json( $this->view );
    }
}

