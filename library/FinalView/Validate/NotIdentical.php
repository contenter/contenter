<?php

/**
* Validate whether given values are not identical
*
*/
class FinalView_Validate_NotIdentical extends Zend_Validate_Identical
{

    /**
     * Error codes
     * @const string
     */
    const SAME      = 'same';

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::SAME      => "The token '%token%' matchs the given token '%value%'",
    );

    protected $_case_insensitive;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @return void
     */
    public function __construct($token = null, $case_insensitive = false)
    {
        $this->setCaseInSensitive($case_insensitive);

        parent::__construct($token);
    }

    public function setCaseInSensitive($case_insensitive = true)
    {
        $this->_case_insensitive = (bool)$case_insensitive;
    }

    public function isCaseInSensitive()
    {
        return $this->_case_insensitive;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue((string) $value);
        $token        = $this->getToken();

        if ($token === null) {
            trigger_error(self::MISSING_TOKEN, E_USER_ERROR);
            return false;
        }

        if (0 === call_user_func($this->_compareMethod(), $value, $this->_token)) {
            $this->_error(self::SAME);
            return false;
        }

        return true;
    }

    private function _compareMethod()
    {
        return $this->_case_insensitive ? 'strcasecmp' : 'strcmp';
    }

}