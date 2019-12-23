# International holidays plugin for Craft CMS 3.x

![Icon](resources/holidays.png)

A plugin for Craft CMS that provides access to many international holidays.

## Background


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

## Settings

You can set the default country and subregion in a config file placed in your CraftCMS config directory. You can find the most recent version in src/config.php. You have to name the file ``holidays.php``.

``` php
return [
    // Default country code in ISO 3166-2 (https://en.wikipedia.org/wiki/ISO_3166-2) notation.
    // E.g. DE-HE, US-CA 
    'defaultCode' => '',
];
```

With ❤ by [codemonauts](https://codemonauts.com)
