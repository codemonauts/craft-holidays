<?php

namespace codemonauts\holidays\services;

use codemonauts\holidays\Holidays;
use craft\base\Component;
use Yasumi\Exception\ProviderNotFoundException;
use Yasumi\Filters\BankHolidaysFilter;
use Yasumi\Filters\ObservedHolidaysFilter;
use Yasumi\Filters\OfficialHolidaysFilter;
use Yasumi\Filters\OtherHolidaysFilter;
use Yasumi\Filters\SeasonalHolidaysFilter;
use Yasumi\Holiday;
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

    public function isTodayHoliday($type = null, $country = null)
    {
        return $this->isHoliday(new \DateTime(), $type, $country);
    }

    public function isHoliday(\DateTime $date, $type = null, $country = null)
    {
        $holidays = $this->getHolidaysOn($date, $type, $country);

        return (bool)$holidays->count();
    }

    public function getTodaysHolidays($type = null, $country = null)
    {
        return $this->getHolidaysOn(new \DateTime(), $type, $country);
    }

    public function getHolidaysOfCurrentWeek($type = null, $country = null)
    {
        $monday = new \DateTime('now', new \DateTimeZone(\Craft::$app->getTimeZone()));

        if ($monday->format('N') != 1) {
            $monday->modify('last monday');
        }

        $sunday = clone $monday;
        $sunday->modify('next sunday');

        return $this->getHolidaysBetween($monday, $sunday, $type, $country);
    }

    public function getHolidaysOn(\DateTime $date, $type = null, $country = null)
    {
        $yasumi = $this->getYasumi($country, $date->format('Y'));

        $holidays = $yasumi->on($date);

        return $this->filterHolidays($holidays, $type);
    }

    public function getHolidaysBetween(\DateTime $startDate, \DateTime $endDate, $type = null, $country)
    {
        $yasumi = $this->getYasumi($country);

        $holidays =  $yasumi->between($startDate, $endDate);

        return $this->filterHolidays($holidays, $type);
    }

    private function getYasumi($code, $year = null, $locale = null)
    {
        if ($code === null) {
            $code = $this->settings->defaultCode;
        }

        if ($year === null) {
            $year = date('Y');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        try {
            $yasumi = Yasumi::createByISO3166_2($code, $year, $locale);
        } catch (ProviderNotFoundException $e) {
            \Craft::error('No provider found for country code "'.$code.'". Please check documentation! Fall back to "US".', 'Holidays');
            $yasumi = Yasumi::createByISO3166_2('US', $year, $locale);
        }

        return $yasumi;
    }

    private function getLocale()
    {
        $locale = \Craft::$app->getLocale();

        return str_replace('-', '_', $locale);
    }

    private function filterHolidays($holidays, $type)
    {
        switch ($type) {

            case Holiday::TYPE_OFFICIAL:
                $filtered = new OfficialHolidaysFilter($holidays);
                break;

            case Holiday::TYPE_BANK:
                $filtered = new BankHolidaysFilter($holidays);
                break;

            case Holiday::TYPE_OBSERVANCE:
                $filtered = new ObservedHolidaysFilter($holidays);
                break;

            case Holiday::TYPE_SEASON:
                $filtered = new SeasonalHolidaysFilter($holidays);
                break;

            case Holiday::TYPE_OTHER:
                $filtered = new OtherHolidaysFilter($holidays);
                break;

            default:
                $filtered = $holidays;
        }

        return $filtered;
    }
}
