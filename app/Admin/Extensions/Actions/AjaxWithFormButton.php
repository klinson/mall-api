<?php
/**
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2019/10/28
 * Time: 10:49
 */

namespace App\Admin\Extensions\Actions;

use Encore\Admin\Admin;

/**
 * 带表单按钮
 * 支持类型"text", "email", "password", "number", "tel", "select", "radio", "checkbox", "textarea", "file" or "url"
 * Class AjaxWithFormButton
 * @package App\Admin\Extensions\Actions
 * @author klinson <klinson@163.com>
 */
class AjaxWithFormButton
{
    // https://sweetalert2.github.io
    // "text", "email", "password", "number", "tel", "select", "radio", "checkbox", "textarea", "file" or "url"

    protected $id;
    protected $title;
    protected $btn_type;
    protected $icon;
    protected $form = [
        'title' => '提交表单',
        'action' => '',
        'method' => 'put',
        'confirm' => '确认',
        'cancel' => '取消',
        'fields' => []
    ];

    public function __construct($title, $form, $inputs, $btn_type = 'primary')
    {
        $this->title = $title;
        $this->form = array_merge($this->form, $form);
        $this->inputs = $inputs;
        $this->btn_type = $btn_type;
    }

    protected function script()
    {
        if (($count = count($this->inputs)) === 1) {

        } else {
            $ajax_data = '{';
            foreach ($this->inputs as $key => &$input) {
                $ajax_data .= $input['name'] . ': input_values['.$key.'],';
                unset($input['name']);
            }
            $ajax_data .= '_token:LA.token}';

            $script = "
$('.{$this->title}-class').unbind('click').click(function() {
  Swal.mixin({
    title: '{$this->form['title']}',
    input: 'text',
    confirmButtonText: '{$this->form['confirm']}',
    cancelButtonText: '{$this->form['cancel']}',
    showCancelButton: true,
    showLoaderOnConfirm: true,
    closeOnConfirm: false,
    progressSteps: ".json_encode(range(1, $count)).",
  }).queue(".json_encode($this->inputs).").then((result) => {
    var input_values = result.value
    $.ajax({
        method: '{$this->form['method']}',
        url: '{$this->form['action']}',
        data: {$ajax_data},
        success: function (data) {
            if (typeof data === 'object') {
                if (data.status) {
                    swal(data.message, '', 'success');
                    $.pjax.reload('#pjax-container');
                } else {
                    swal(data.message, '', 'error');
                }
            }
        }
    });
  });
});
";
        }


        return $script;
    }

    protected function render()
    {
//        Admin::js(static::$js);
//        Admin::css(static::$css);
        Admin::script($this->script());

        return <<<EOT
&nbsp;<a href="javascript:void(0);" data-action="{$this->form['action']}" class="{$this->title}-class btn btn-xs btn-{$this->btn_type}">
    {$this->title}
</a>&nbsp;
EOT;
    }

    public function __toString()
    {
        return $this->render();
    }

}