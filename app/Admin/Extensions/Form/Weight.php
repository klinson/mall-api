<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2018/10/24
 * Time: 21:24
 */

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field\Text;

class Weight extends Text
{
    /**
     * @var string
     */
    protected $append = 'kg';

    /**
     * @var array
     */
    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js',
    ];

    public function __construct($column, array $arguments = [])
    {
        parent::__construct($column, $arguments);
    }

    /**
     * Set symbol for currency field.
     *
     * @param string $symbol
     *
     * @return $this
     */
    public function symbol($append)
    {
        $this->append = $append;

        return $this;
    }

    /**
     * Set digits for input number.
     *
     * @param int $digits
     *
     * @return $this
     */
    public function digits($digits)
    {
        return $this->options(compact('digits'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($value)
    {
        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->script = <<<EOT
$('{$this->getElementClassSelector()}').inputmask({
    alias: "numeric",
    radixPoint: ".",
    prefix: "",
    digits: 4,
    removeMaskOnSubmit: true
    
});
EOT;

//        $this
//            ->append($this->append)
//            ->defaultAttribute('style', 'width: 120px');

        return parent::render();
    }
}
