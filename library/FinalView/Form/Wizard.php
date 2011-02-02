<?php

/**
 * Multi-Page Forms Wizard
 *
 * @author dV
 */
class FinalView_Form_Wizard
{

    /**
     * Current session key
     *
     * @var string
     */
    private $_session_key;

    /**
     * Forms
     *
     * @var Zend_Form
     */
    private $_forms = array();

    /**
     * Session form wizard helper
     *
     * @var FinalView_Controller_Action_Helper_SessionFormWizard
     */
    private $_session_helper;


    public function __construct(array $forms)
    {
        if (count($forms) < 2) {
            trigger_error('Number of forms must be more than 1', E_USER_ERROR);
        }
        $this->_forms = $forms;


        $this->_session_helper =
            Zend_Controller_Action_HelperBroker::getStaticHelper('SessionFormWizard');


        $this->_initSession();


        $this->_validateStep($this->_getCurrentStep());
    }

    /**
     * Init wizard: start session and redirect with current session key and step
     * if session not exists
     *
     */
    private function _initSession()
    {
        $this->_session_key = $this->getRequest()->getParam('session_key');

        if (is_null($this->_session_key) || !$this->_session_helper->exists($this->_session_key)) {
            $this->_session_key = $this->_session_helper->start();

            $this->_redirect();
        }
    }

    /**
     * Redirect to the specified step (current one by default)
     *
     * @param null|string $step
     */
    private function _redirect($step = null)
    {
        if (is_null($step)) {
            $step = current($this->_getSteps());
        }

        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
            ->gotoUrl((http_build_url(
                Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(),
                array('query' => array(
                    'step' => urlencode($step),
                    'session_key' => $this->_session_key))
            )));
    }

    /**
     * Validate given step
     *
     */
    private function _validateStep($step)
    {
        // whether such step exists
        if (!in_array($step, $this->_getSteps())) {
            Zend_Controller_Action_HelperBroker::getStaticHelper('error')
                ->notFound('Step does not exist');
        }

        // whether prev step is stored
        if ($this->_getPrevStep($step) && !in_array($this->_getPrevStep($step),
            array_keys($this->_session_helper->get($this->_session_key))))
        {
            Zend_Controller_Action_HelperBroker::getStaticHelper('error')
                ->notFound('Step is out of turn');
        }
    }

    /**
     * Return current step form
     *
     * @return Zend_Form
     */
    public function getCurrentForm()
    {
        $sub_form = $this->_forms[$this->_getCurrentStep()];
        return $sub_form->populate($this->getStoredData($this->_getCurrentStep()));
    }

    /**
     * Return the request object.
     *
     * @return null|Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    /**
     * Save current step form data and redirect to the next one
     *
     */
    public function save(array $data)
    {
        $this->_session_helper->set(
            $this->_session_key,
            $this->_getCurrentStep(),
            $data
            );

        if ($step = $this->_getNextStep()) {
            $this->_redirect($step);
        }
    }

    public function getStoredData($step = null)
    {
        $data = $this->_session_helper->get($this->_session_key);
        if (is_null($step)) {
            $this->_session_helper->destroy($this->_session_key);
            return $data;
        } else {
            $this->_validateStep($step);
            return array_key_exists($step, $data) ? $data[$step] : array();
        }
    }

    public function isFirstStep()
    {
        return false === $this->_getPrevStep();
    }

    public function isLastStep()
    {
        return false === $this->_getNextStep();
    }

    private function _getCurrentStep()
    {
        return urldecode($this->getRequest()->getParam('step', current($this->_getSteps())));
    }

    private function _getSteps()
    {
        return array_keys($this->_forms);
    }

    private function _getNextStep($step = null)
    {
        return $this->_stepOffset(+1, $step);
    }

    private function _getPrevStep($step = null)
    {
        return $this->_stepOffset(-1, $step);
    }

    private function _stepOffset($offset, $step = null)
    {
        if (is_null($step)) {
            $step = $this->_getCurrentStep();
        }
        $steps = $this->_getSteps();

        return false !== ($current = array_search($step, $steps))
            && array_key_exists($key = $current + $offset, $steps) ? $steps[$key] : false;
    }

}