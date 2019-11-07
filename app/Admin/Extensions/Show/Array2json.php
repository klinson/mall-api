<?php

namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class Array2json extends AbstractField
{
    public function render()
    {
        $this->border = false;
        $this->escape = false;

        return '<pre><code>'.json_encode($this->value, JSON_PRETTY_PRINT).'</code></pre>';
    }
}
