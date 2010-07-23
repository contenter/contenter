<?php

class FinalView_View_Helper_FormCcExpDate extends Zend_View_Helper_FormElement 
{
    
    const DEFAULT_ELEMENT_SEPARATOR = ' ';
    
    public function formCcExpDate($name, $value = null, $attribs = null)
    {
        $value 
            ? list($month, $year) = explode('-', $value)
            : $month = $year = 0;
        
        $separator = $this->_getSeparator($attribs);
        $month_options = $this->_getMonthOptions($attribs);
        $year_options = $this->_getYearOptions($attribs);
        
        // output
        return
            $this->view->formSelect
            (
                $name . '[month]',
                $month,
                $attribs,
                $month_options
            )
            . $separator . 
            $this->view->formSelect
            (
                $name . '[year]',
                $year,
                $attribs,
                $year_options
            )
            ;
    }
    
    private function _getSeparator(array &$attribs) 
    {
        if (isset($attribs['separator'])) {
            $separator = $attribs['separator'];
            unset($attribs['separator']);
        } else {
            $separator = self::DEFAULT_ELEMENT_SEPARATOR;
        }
        
        return $separator;
    }
    
    private function _getMonthOptions(array &$attribs) 
    {
        if (isset($attribs['month_format'])) {
            $month_format = $attribs['month_format'];
            unset($attribs['month_format']);
        } else {
            $month_format = 'M';
        }
        
        for ($i = 1, $monthOptions = array('Month'); $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1, 0);
            $monthOptions[date('m', $timestamp)] = date($month_format, $timestamp);
        }
        
        return $monthOptions;
    }
    
    private function _getYearOptions(array &$attribs) 
    {
        // years
        if (isset($attribs['year_format'])) {
            $year_format = $attribs['year_format'];
            unset($attribs['year_format']);
        } else {
            $year_format = 'Y';
        }
        if (isset($attribs['start_year'])) {
            $start_year = $attribs['start_year'];
            unset($attribs['start_year']);
        } else {
            $start_year = date($year_format);
        }
        if (isset($attribs['end_year'])) {
            $end_year = $attribs['end_year'];
            unset($attribs['end_year']);
        } else {
            $end_year = date($year_format);
        }
        // always try to convert year to string with left 0 if it's needed
        $end_year = str_pad($end_year, 2, 0, STR_PAD_LEFT);
        $year_range_keys = $year_range_values = array_map
        (   create_function('$year', 'return str_pad($year, 2, 0, STR_PAD_LEFT);'),
            range($start_year, $end_year)
        );
        if ('y' == $year_format) {
            $year_range_values = range('20' . $start_year, '20' . $end_year);
        }
        
        return array('Year') + array_combine($year_range_keys, $year_range_values);
    }
    
}
