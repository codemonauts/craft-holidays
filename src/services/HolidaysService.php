<?php

namespace codemonauts\holidays\services;

use codemonauts\holidays\Holidays;
use craft\base\Component;
use craft\helpers\DateTimeHelper;
use Yasumi\Exception\ProviderNotFoundException;
use Yasumi\Yasumi;

class HolidaysService extends Component
{
    private $settings;

    private $fallbackCode = 'US';

    public function init()
    {
        $this->settings = Holidays::getInstance()->getSettings();

        parent::init();
    }

    public function isTodayHoliday($country = null)
    {
        return $this->isHoliday(new \DateTime(), $country);
    }

    public function isHoliday(\DateTime $date, $country = null)
    {
        $yasumi = $this->getYasumi($country);

        return $yasumi->isHoliday($date);
    }

    public function getHolidaysOfCurrentWeek($country = null)
    {
        $monday = new \DateTime('monday this week');
        $sunday = new \DateTime('sunday this week');

        return $this->getHolidaysBetween($monday, $sunday, $country);
    }

    public function getHolidaysBetween(\DateTime $startDate, \DateTime $endDate, $country)
    {
        $yasumi = $this->getYasumi($country);

        print_r($yasumi->between($startDate, $endDate));die;
    }

    private function getYasumi($code)
    {
        if ($code === null) {
            return $this->settings->defaultCode;
        }

        try {
            $yasumi = Yasumi::createByISO3166_2($code);
        } catch (ProviderNotFoundException $e) {
            \Craft::error('No provider found for country code "'.$code.'". Please check documentation! Fall back to "US".', 'Holidays');
            $yasumi = Yasumi::createByISO3166_2('US');
        }

        return $yasumi;
    }
}
