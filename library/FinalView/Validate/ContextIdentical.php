<?php

/**
 * Compare element's value with value of the given key from context
 *
 */
class FinalView_Validate_ContextIdentical extends Zend_Validate_Identical
{

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
        $context = func_get_arg(1);

        if (!array_key_exists($this->getToken(), $context)) {
            trigger_error(self::MISSING_TOKEN, E_USER_ERROR);
        }

        $token = $context[$this->getToken()];
        if ($value !== $token)  {
            $this->_error(self::NOT_SAME);
            return false;
        }

        return true;
    }

}