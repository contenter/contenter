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
            $this->_error(self::MISSING_TOKEN);
            return false;
        }

        if ($value == $token)  {
            $this->_error(self::SAME);
            return false;
        }

        return true;
    }
    
}