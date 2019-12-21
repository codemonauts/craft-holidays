<?php

namespace codemonauts\holidays\variables;

use codemonauts\holidays\Holidays;

class HolidaysVariable
{
    public function isTodayHoliday($country = null)
    {
        return Holidays::getInstance()->holidays->isTodayHoliday($country);
    }

    public function getHolidaysOfCurrentWeek($country = null)
    {
        return Holidays::getInstance()->holidays->getHolidaysOfCurrentWeek($country);
    }
}

