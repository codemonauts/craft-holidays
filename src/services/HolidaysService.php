<?php

namespace codemonauts\holidays\services;

use codemonauts\holidays\base\Holidays;
use Craft;
use craft\base\Component;
use DateTime;
use DateTimeZone;
use Iterator;
use Yasumi\ProviderInterface;

class HolidaysService extends Component
{
    public function isTodayHoliday(?string $type = null, ?string $country = null): bool
    {
        return Holidays::find()
            ->country($country)
            ->type($type)
            ->on(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
            ->isHoliday();
    }

    public function getTodaysHolidays($type = null, $country = null): ProviderInterface|Iterator
    {
        return Holidays::find()
            ->country($country)
            ->type($type)
            ->on(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
            ->all();
    }

    public function getHolidaysOfCurrentWeek($type = null, $country = null): ProviderInterface|Iterator
    {
        $monday = new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));

        if ($monday->format('N') != 1) {
            $monday->modify('last monday');
        }

        $sunday = clone $monday;
        $sunday->modify('next sunday');

        return Holidays::find()
            ->country($country)
            ->type($type)
            ->between($monday, $sunday)
            ->all();
    }
}
