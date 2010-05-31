<?php

class ErrorController extends FinalView_Controller_Action
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
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = $errors->exception->getMessage();
            break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                $error_code = $errors->exception->getCode() ? $errors->exception->getCode() : 500;
                $this->getResponse()->setHttpResponseCode($error_code);
                $this->view->message = $errors->exception->getMessage();
            break;
            default:                 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
            break;
        }

        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }    
}

