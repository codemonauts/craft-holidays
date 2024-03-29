<?php

namespace codemonauts\holidays\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string Default country code in ISO 3166-2 (https://en.wikipedia.org/wiki/ISO_3166-2) notation.
     */
    public string $defaultCode = '';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['defaultCode', 'required'],
            ['defaultCode', 'string'],
        ];
    }
}
