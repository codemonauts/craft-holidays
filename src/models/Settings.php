<?php

namespace codemonauts\holidays\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string The Instagram account to get the feed from
     */
    public $defaultCode = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['defaultCode', 'required'],
            ['defaultCode', 'string'],
        ];
    }
}
