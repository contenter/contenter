<?php
/**
 */
class FinalView_Form_Element_DateTime extends Zend_Form_Element_Xhtml

{
    /**
     * Use formDate view helper
     * @var string
     */
    public $helper = 'formDateTime';

    protected $_outDateFormat = 'Y-m-d';

    protected $_timezone = 'UTC';

    protected $_inDateFormat = 'Y-m-d H:i:s';


    public function setTimeZone($tz)
    {
        $this->setAttrib('timezone', $tz);
        $this->_timezone = $tz;
        return $this;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    public function getValue()
    {
        if (empty($this->_value)) {
            return '';
        }

        $dateTS = strtotime($this->_value['date'].' '.$this->_timezone);

        if (!$dateTS) return '';

        if(!is_numeric(@$this->_value['time']['hours']) || $this->_value['time']['hours'] < 0 || $this->_value['time']['hours'] > 23) return '';

        if(!is_numeric(@$this->_value['time']['minutes']) || $this->_value['time']['minutes'] < 0 || $this->_value['time']['minutes'] > 59) return '';

        $dateTS += $this->_value['time']['hours'] * 60 * 60 + $this->_value['time']['minutes'] * 60;

        return date($this->_inDateFormat, $dateTS);
    }

    public function setOutDateFormat($format)
    {
        $this->setAttrib('dateFormat', $format);
        $this->_outDateFormat = $format;
        return $this;
    }

    public function getOutDateFormat()
    {
        return $this->_outDateFormat;
    }

    public function setInDateFormat($format)
    {
        $this->_inDateFormat = $format;
        return $this;
    }

    public function getInDateFormat()
    {
        return $this->_inDateFormat;
    }

    public function setValue($value)
    {
        if (is_string($value)) {
            $ts = strtotime($value);
            if ($ts > 0) {
                $currTz = date_default_timezone_get();
                date_default_timezone_set($this->_timezone);
                $date = array(
                    'date'  =>  date($this->_outDateFormat, $ts),
                    'time'  =>  array(
                        'hours'     =>  date('G', $ts),
                        'minutes'   =>  (int)date('i', $ts)
                    )
                );
                date_default_timezone_set($currTz);
                return parent::setValue($date);
            }
            return parent::setValue(null);
        }
        return parent::setValue($value);
    }
}
