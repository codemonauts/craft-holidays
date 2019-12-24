<?php

namespace codemonauts\holidays\base;

use Craft;
use craft\helpers\DateTimeHelper;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Iterator;
use Yasumi\Exception\ProviderNotFoundException;
use Yasumi\Filters\BankHolidaysFilter;
use Yasumi\Filters\ObservedHolidaysFilter;
use Yasumi\Filters\OfficialHolidaysFilter;
use Yasumi\Filters\OtherHolidaysFilter;
use Yasumi\Filters\SeasonalHolidaysFilter;
use Yasumi\Holiday;
use Yasumi\Provider\AbstractProvider;
use Yasumi\Yasumi;

class Holidays
{
    /**
     * @var integer
     */
    private $_year;

    /**
     * @var string
     */
    private $_country;

    /**
     * @var null|string
     */
    private $_type = null;

    /**
     * @var null|DateTime
     */
    private $_startDate = null;

    /**
     * @var null|DateTime
     */
    private $_endDate = null;

    /**
     * @var string
     */
    private $_locale;

    public function __construct()
    {
        $this->_year = date('Y');

        $this->_locale = $this->getLocale();
    }

    public static function find(): self
    {
        return new self();
    }

    public function year(int $year): self
    {
        $this->_year = $year;

        return $this;
    }

    public function country(string $code): self
    {
        $this->_country = $code;

        return $this;
    }

    public function type($type): self
    {
        $this->_type = $type;

        return $this;
    }

    public function locale(string $locale): self
    {
        $this->_locale = str_replace('-', '_', $locale);

        return $this;
    }

    public function on($date): self
    {
        $this->_startDate = $this->parseDate($date);

        return $this;
    }

    public function between($startDate, $endDate): self
    {
        $this->_startDate = $this->parseDate($startDate);
        $this->_endDate = $this->parseDate($endDate);

        return $this;
    }

    public function one()
    {
        return $this->getHolidays()->current();
    }

    public function all()
    {
        return $this->getHolidays();
    }

    public function count()
    {
        return $this->getHolidays()->count();
    }

    public function isHoliday()
    {
        return (bool)$this->getHolidays()->count();
    }

    private function getYasumi(): AbstractProvider
    {
        if ($this->_country === null) {
            $this->_country = \codemonauts\holidays\Holidays::getInstance()->getSettings()->defaultCode;
        }

        if ($this->_startDate !== null) {
            $this->_year = $this->_startDate->format('Y');
        }

        try {
            $yasumi = Yasumi::createByISO3166_2($this->_country, $this->_year, $this->_locale);
        } catch (ProviderNotFoundException $e) {
            Craft::error('No provider found for country code "' . $this->_country . '". Please check documentation! Fall back to "US".', 'Holidays');
            $yasumi = Yasumi::createByISO3166_2('US', $this->_year, $this->_locale);
        }

        return $yasumi;
    }

    private function filterType(Iterator $holidays, string $type): Iterator
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
                throw new Exception('Unknown type of filter: "' . $type . '"');
        }

        return $filtered;
    }

    private function getLocale(): string
    {
        $locale = Craft::$app->getLocale();

        return str_replace('-', '_', $locale);
    }

    private function getHolidays()
    {
        $holidays = $this->getYasumi();

        if ($this->_startDate !== null) {
            if ($this->_endDate !== null) {
                $holidays = $holidays->between($this->_startDate, $this->_endDate);
            } else {
                $holidays = $holidays->on($this->_startDate);
            }
        }

        if ($this->_type !== null) {
            $holidays = $this->filterType($holidays, $this->_type);
        }

        return $holidays;
    }

    private function parseDate($date)
    {
        if (is_a($date, DateTimeInterface::class)) {
            return $date;
        }

        $newDate = DateTimeHelper::toDateTime($date, true, true);

        if (!$newDate) {
            $newDate = strtotime($date);
            $newDate = new DateTime('@' . $newDate, new DateTimeZone(Craft::$app->getTimeZone()));
            if (!$newDate) {
                throw new Exception('Cannot parse given date');
            }
        }

        return $newDate;
    }
}
