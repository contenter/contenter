<?php

class FinalView_View_Helper_FormDateTime extends Zend_View_Helper_FormElement
{

    public function formDateTime ($name, $value = null, $attribs = null)
    {

        $date = '';
        $time = array(
            'hours'     =>  'label',
            'minutes'   =>  'label'
        );

        if (is_string($value)) {
            $dateFormat = 'Y-m-d';

            if (isset($attribs['dateFormat'])) {
                $dateFormat = $attribs['dateFormat'];
            }
            $timezone = 'UTC';

            if (isset($attribs['timezone'])) {
                $timezone = $attribs['timezone'];
            }
            $ts = strtotime($value);
            if ($ts) {
                $currTz = date_default_timezone_get();
                date_default_timezone_set($timezone);
                $date = date($dateFormat, $ts);
                $time = array(
                    'hours'     =>  date('G', $ts),
                    'minutes'   =>  (int)date('i', $ts)
                );
                date_default_timezone_set($currTz);
            }
        }

        $hoursOptions = array('label' => 'Hours');

        $hours = range(0, 23);
        if (@$attribs['time_format'] == '24') {
            $hoursOptions += array_combine($hours, $hours);
        }else{
            for ($i = 0; $i < 24; $i ++)
            {
                $hoursValues[$i] = date('ga', mktime(
                    $i,0,0,1,1,1990
                ));
            }

            $hoursOptions += $hoursValues;
        }

        $minutesOptions = array('label' => 'Minutes', 0  =>  ':00', 15 =>  ':15', 30 =>  ':30', 45 =>  ':45');

        // output
        return
            '<div class="datetime-container">'.$this->view->formText
            (
                $name . '[date]',
                $date,
                @$attribs['date']
            )
            . '<div class="date-time-separator"></div>' .
            $this->view->formSelect
            (
                $name . '[time][hours]',
                $time['hours'],
                @$attribs['time'],
                $hoursOptions
            )
            . '<div class="hours-minutes-separator"></div>' .
            $this->view->formSelect
            (
                $name . '[time][minutes]',
                $time['minutes'],
                @$attribs['time'],
                $minutesOptions
            ).'</div>'
            ;
    }

}
