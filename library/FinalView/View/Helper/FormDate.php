<?php
  
class FinalView_View_Helper_FormDate extends Zend_View_Helper_FormElement 
{
    
    const DEFAULT_ELEMENT_SEPARATOR = ' ';
    
    public function formDate ($name, $value = null, $attribs = null)
    {
        if($value) {
            list($year, $month, $day) = explode('-', $value);
        } else {
            $year = $month = $day = 0;
        }
        
        // set null values ?
        if (isset($attribs['null'])) {
            $null = (bool)$attribs['null'];
            unset($attribs['null']);
        } else {
            $null = true;
        }
        
        // years
        if (isset($attribs['start_year'])) {
            $start_year = $attribs['start_year'];
            unset($attribs['start_year']);
        } else {
            $start_year = date('Y');
        }
        if (isset($attribs['end_year'])) {
            $end_year = $attribs['end_year'];
            unset($attribs['end_year']);
        } else {
            $end_year = date('Y');
        }
        $year_range = range($start_year, $end_year);
        $yearOptions = $null ? array('Year') : array();
        $yearOptions += array_combine($year_range, $year_range);
        
        // months
        for ($i = 1, $monthOptions = $null ? array('Month') : array(); $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1, 0);
            $monthOptions[date('m', $timestamp)] = date('M', $timestamp);
        }
        
        // days
        for ($i = 1, $dayOptions = $null ? array('Day') : array(); $i <= 31; $i ++) {
            $dayOptions[str_pad($i, 2, 0, STR_PAD_LEFT)] = $i;
        }
        
        // "select" separator
        if (isset($attribs['separator'])) {
            $separator = $attribs['separator'];
            unset($attribs['separator']);
        } else {
            $separator = self::DEFAULT_ELEMENT_SEPARATOR;
        }
        
        // output
        return
            $this->view->formSelect
            (
                $name . '[year]',
                $year,
                $attribs,
                $yearOptions
            )
            . $separator . 
            $this->view->formSelect
            (
                $name . '[month]',
                $month,
                $attribs,
                $monthOptions
            )
            . $separator . 
            $this->view->formSelect
            (
                $name . '[day]',
                $day,
                $attribs,
                $dayOptions
            )
            ;
    }
    
}
