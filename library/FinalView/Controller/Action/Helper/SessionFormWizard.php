<?php

/**
 * Session helper. Addicted to the "FinalView_Form_Wizard"
 *
 * @author dV
 */
class FinalView_Controller_Action_Helper_SessionFormWizard
    extends Zend_Controller_Action_Helper_Abstract
{

    const SESSION_NAMESPACE = 'form_wizard';

    private $_session;

    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
    }

    public function start()
    {
        $session_key = md5(uniqid(rand()));
        $this->_session->{$session_key} = array();
        return $session_key;
    }

    public function get($session_key)
    {
        return $this->_session->{$session_key};
    }

    public function set($session_key, $step, array $data)
    {
        $this->_session->{$session_key} = array_merge(
            $this->get($session_key), array($step => $data));
    }

    public function exists($session_key)
    {
        return isset($this->_session->{$session_key});
    }

    public function destroy($session_key)
    {
        unset($this->_session->{$session_key});
    }

}