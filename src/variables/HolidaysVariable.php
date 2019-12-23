<?php

namespace codemonauts\holidays\variables;

use codemonauts\holidays\Holidays;

class HolidaysVariable
{
    public function isTodayHoliday($type = null, $country = null)
    {
        return Holidays::getInstance()->holidays->isTodayHoliday($type, $country);
    }

    public function getHolidaysOfCurrentWeek($type = null, $country = null)
    {
        return Holidays::getInstance()->holidays->getHolidaysOfCurrentWeek($type, $country);
    }
}
