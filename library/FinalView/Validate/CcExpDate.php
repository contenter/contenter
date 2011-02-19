<?php

class FinalView_Validate_CcExpDate extends Zend_Validate_Abstract
{

    private $_start_year;

    private $_end_year;

    const YEAR_FORMAT = 'Y';

    const INVALID_DATE   = 'dateInvalidDate';
    const YEAR_OUT_OF_RANGE   = 'yearOutOfRange';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_DATE        => '"%value%" does not appear to be a valid date',
        self::YEAR_OUT_OF_RANGE => 'Given year "%value%" is out of range',
    );

    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp['end_year'] = array_shift($options);
            if (!empty($options)) {
                $temp['start_year'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('end_year', $options)) {
            $this->_end_year = $this->_formatYear($options['end_year']);
        }

        $this->_start_year = array_key_exists('start_year', $options)
            ? $this->_formatYear($options['start_year'])
            : date(self::YEAR_FORMAT);
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
        if (!is_string($value)) {
            trigger_error('Value must be of string format', E_USER_ERROR);
        }

        $this->_setValue($value);

        list($month, $year) = array_values(explode('-', $value));

        if ($month <= 0) {
            $this->_error(self::INVALID_DATE);
            return false;
        }

        if ($year <= 0) {
            $this->_error(self::INVALID_DATE);
            return false;
        }

        if (!is_null($this->_end_year)) {
            $year = $this->_formatYear($year);
            if ($year > $this->_end_year || $year < $this->_start_year) {
                $this->_error(self::YEAR_OUT_OF_RANGE, $year);
                return false;
            }
        }

        return true;
    }

    private function _formatYear($year)
    {
        return date(self::YEAR_FORMAT, mktime(0, 0, 0, 1, 1, $year));
    }

}