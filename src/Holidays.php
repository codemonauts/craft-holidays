<?php

namespace codemonauts\holidays;

use codemonauts\holidays\models\Settings;
use codemonauts\holidays\services\HolidaysService;
use codemonauts\holidays\variables\HolidaysVariable;
use Craft;
use craft\base\Plugin;
use craft\helpers\Cp;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use Yasumi\Yasumi;
use yii\base\Event;

/**
 * @property HolidaysService holidays
 */
class Holidays extends Plugin
{
    /**
     * @var \codemonauts\holidays\Holidays
     */
    public static Holidays $plugin;

    /**
     * @var \codemonauts\holidays\models\Settings|null
     */
    public static ?Settings $settings;

    /**
     * @inheritDoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        self::$settings = self::$plugin->getSettings();

        $this->setComponents([
            'holidays' => HolidaysService::class,
        ]);

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $variable = $event->sender;
            $variable->attachBehaviors([
                HolidaysVariable::class,
            ]);
        });
    }

    /**
     * @inheritDoc
     */
    public function afterInstall(): void
    {
        parent::afterInstall();

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('settings/plugins/holidays')
        )->send();
    }

    /**
     * @inheritDoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritDoc
     */
    protected function settingsHtml(): ?string
    {
        $providers = Yasumi::getProviders();

        ksort($providers);

        return Cp::selectizeFieldHtml([
            'label' => Craft::t('holidays', 'Country'),
            'id' => 'defaultCode',
            'name' => 'defaultCode',
            'instructions' => Craft::t('holidays', 'Please, select the default country or country and subregion.'),
            'value' => self::$settings->defaultCode,
            'options' => $providers,
            'required' => true,
            'includeEnvVars' => true,
        ]);
    }

    /**
     * Returns the holidays component.
     *
     * @return HolidaysService
     * @throws \yii\base\InvalidConfigException
     */
    public function getHolidays(): HolidaysService
    {
        return $this->get('holidays');
    }
}
