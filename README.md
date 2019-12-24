# International holidays plugin for Craft CMS

![Icon](resources/holidays.png)

A plugin for Craft CMS that provides access to many international holidays.

This plugin is based on the awesome work of [yasumi](https://azuyalabs.github.io/yasumi/). Go and buy him a coffee!

## Background

This plugin provides easy access to holidays in many countries. For example to automatically determine if a shop or restaurant is open or closed on an official holiday in a country.

## Coverage

Currently 35 countries and 85 sub-regions:

Australia,
Austria,
Belgium,
Bosnia & Herzegovina,
Brazil,
Croatia,
Czechia,
Denmark,
Estonia,
Finland,
France,
Germany,
Greece,
Hungary,
Ireland,
Italy,
Japan,
Latvia,
Lithuania,
Netherlands,
New Zealand,
Norway,
Poland,
Portugal,
Romania,
Russia,
Slovakia,
South Africa,
South Korea,
Spain,
Sweden,
Switzerland,
United States,
Ukraine and 
United Kingdom

For more details see [yasumi's feature page](https://azuyalabs.github.io/yasumi/features/). And if you are missing your country, why not contribute by adding a new provider for your country?

## Requirements

 * Craft CMS >= 3.0.0

## Installation

Open your terminal and go to your Craft project:

``` shell
cd /path/to/project
composer require codemonauts/craft-holidays
./craft install/plugin holidays
```

Switch to the settings page in the control panel and enter select your default country and subregion.  

## Usage

You can query for holidays like for entries:

``` twig
<ul>
{% for holiday in craft.holidays.type('official').between('last monday', '2019-12-31').all() %}
<li>{{ holiday.format('Y-m-d') }}: {{ holiday.getName() }}</li>
{% endfor %}
</ul>
```

or same in php:

``` php
use codemonauts\holidays\base\Holidays;

$holidays = Holidays::find()
    ->type('official')
    ->between('last monday', '2019-12-31')
    ->all();
 ```

### Filtering

You can combine the following filters in any order:

`type(typename)` specifies the type of holidays to return. Default is `Null`. Possible other types are:
* 'official'
* 'observance'
* 'season'
* 'bank'
* 'other'
* Null (means "all")

`country(code)` sets the country for which the holidays should be returned. The code is the [ISO 3166-2](https://en.wikipedia.org/wiki/ISO_3166-2) code of the country and its subregion. You can set the default country in the settings.

`locale(code)` sets the locale code to translate the holidays names to. The default is set to your site locale in Craft CMS.

`year(year)` specifies for which year the holidays are returned. You can only get the holidays for one specific year. This is set automatically to the date set by `on()` or `between()`. Normally you do not need to specify anything here.    

`on(date)` sets a single date to check for holidays. You can provide a DateTimeInterface or a date string like '2019-12-25' or a string that can be parsed by [strtotime](https://www.php.net/manual/en/datetime.formats.php).

`between(startDate, endDate)` filter for a time range. Same rules as for `on(date)`.

### Fetching

To fetch the results you have different functions:

`one()` returns the first holiday as an extended DateTime object (see results).

`all()` returns all holidays as array (actually an iterator) of extended DateTime objects.

`count()` returns the number of holidays found.

`isHoliday()` returns true or false if a holiday exists.

### Results

As result you get a DateTime object with the following extensions:

`getType()` returns the type of holiday.

`getName()` returns the translated name of the holiday. If for the given locale no translation is defined, the name in 'en_US' is returned.

## Examples

Get all holidays of a year:

``` twig
craft.holidays.year(2019).all()
 ```

Get all official holidays of a time range:

``` twig
craft.holidays.type('official').between(entry.postDate, '2019-12-31').all()
 ```

Check if today is a official holiday in Japan:

``` twig
craft.holidays.type('official').on('now').country('JP').isHoliday()
 ```

## Settings

You can set the default country and subregion in a config file placed in your CraftCMS config directory. You can find the most recent version in src/config.php. You have to name the file ``holidays.php``.

``` php
return [
    // Default country code in ISO 3166-2 (https://en.wikipedia.org/wiki/ISO_3166-2) notation.
    // E.g. DE-HE, US-CA 
    'defaultCode' => '',
];
```

If you do not set a default country code or the country code could not be found, we fall back to `US`.

With ❤ by [codemonauts](https://codemonauts.com)
