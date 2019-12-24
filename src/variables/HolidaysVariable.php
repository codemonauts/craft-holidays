<?php

namespace codemonauts\holidays\variables;

use codemonauts\holidays\base\Holidays;
use yii\base\Component;

class HolidaysVariable extends Component
{
    public function attach($component)
    {

    }

    public function holidays()
    {
        return Holidays::find();
    }
}
