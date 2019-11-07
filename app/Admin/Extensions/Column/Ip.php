<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Column;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Ip extends AbstractDisplayer
{
    public function display()
    {
        return long2ip($this->value);
    }
}
