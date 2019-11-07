<?php

namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class Ip extends AbstractField
{
    public function render()
    {
        return long2ip($this->value);
    }
}
