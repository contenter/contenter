<?php

/**
 * Class for Database record validation
 *
 */
abstract class FinalView_Validate_Db_Abstract extends Zend_Validate_Abstract
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array
    (
        self::ERROR_NO_RECORD_FOUND => 'No record matching %value% was found',
        self::ERROR_RECORD_FOUND    => 'A record matching %value% was found'
    );

    /**
     * @var string
     */
    protected $_model = '';

    /**
     * @var string
     */
    protected $_selector = '';

    protected $_selectors = array();

    public function __construct($model, $selector, $selectors = array())
    {
        $this->_model   = (string) $model;
        $this->_selector   = (string) $selector;

        $this->_selectors = $selectors;
    }

    public function setSelectors(array $selectors = array())
    {
        $this->_selectors = $selectors;
    }

    public function addSelector($selector, $value)
    {
        $this->_selectors[$selector] = $value;
    }

    public function removeSelector($selector)
    {
        unset($this->_selectors[$selector]);
    }

    public function getSelectorValue($selector)
    {
        return array_key_exists($selector, $this->_selectors) ? $this->_selectors[$selector] : null;
    }

    protected function _getRecordsCount($value)
    {
        if (empty($this->_model) || empty($this->_selector)) {
            trigger_error('Model and selector must be defined');
        }

        $selectors = $this->_selectors + array($this->_selector => $value);
        return Doctrine::getTable($this->_model)->countByParams($selectors);
    }
}
