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

                // 404 error -- controller or action not found
                /*$this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';*/
                $this->abortAction($errors->exception->getMessage());
                break;
            default:
                // application error 
                //$this->getResponse()->setHttpResponseCode(500);
                //$this->view->message = 'Application error';
                $this->internalErrorAction($errors->exception->getMessage());
                break;
        }

        /*$this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;*/
    }
    
    /**
    * 404
    * 
    * @param string $message
    */
    public function abortAction($message) 
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = $message;
    }
    
    /**
    * 403
    * 
    * @param string $message
    */
    public function denyAction($message) 
    {
        $this->getResponse()->setHttpResponseCode(403);
        $this->view->message = $message;
    }
    
    /**
    * 500
    * 
    * @param string $message
    */
    public function internalErrorAction($message) 
    {
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->message = $message;
    }
    
}

