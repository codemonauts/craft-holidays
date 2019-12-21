<?php

namespace codemonauts\holidays;

use codemonauts\holidays\models\Settings;
use codemonauts\holidays\services\HolidaysService;
use codemonauts\holidays\variables\HolidaysVariable;
use Craft;
use craft\base\Plugin;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use Yasumi\Yasumi;
use yii\base\Event;

/**
 * Class Holidays
 * @property HolidaysService holidays
 * @package codemonauts\holidays
 */
class Holidays extends Plugin
{
    public $hasCpSettings = true;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'holidays' => HolidaysService::class,
        ]);

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $variable = $event->sender;
            $variable->set('holidays', HolidaysVariable::class);
        });
    }

    /**
     * @inheritDoc
     */
    public function afterInstall()
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
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritDoc
     */
    protected function settingsHtml()
    {
        $providers = Yasumi::getProviders();

        ksort($providers);

        return Craft::$app->getView()->renderTemplate('holidays/settings', [
                'settings' => $this->getSettings(),
                'providers' => $providers,
            ]
        );
    }
}
