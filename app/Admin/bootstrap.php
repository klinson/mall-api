<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Form;
use Encore\Admin\Grid\Column;
use Encore\Admin\Show;

Admin::js('/vendor/clipboard/dist/clipboard.min.js');
Admin::script("document.getElementsByTagName('footer')[0].getElementsByTagName('strong')[0].innerHTML='".config('admin.powered_by_info')."';");

Form::forget('map');

// 编辑器
//Form::forget('editor');
Form::extend('ckEditor', \App\Admin\Extensions\Form\CKEditor::class);
Form::extend('codeEditor', \App\Admin\Extensions\Form\CodeEditor::class);
Form::extend('markdown', \App\Admin\Extensions\Form\MarkdownEditor::class);
Form::extend('media', \Encore\FileBrowser\FileBrowserField::class);
Column::extend('currency', \App\Admin\Extensions\Column\Currency::class);
Form::extend('currency', \App\Admin\Extensions\Form\Currency::class);
Form::extend('weight', \App\Admin\Extensions\Form\Weight::class);
Form::extend('areaCheckbox', \App\Admin\Extensions\Form\AreaCheckbox::class);

//Column::extend('qrcode', \App\Admin\Extensions\Column\Qrcode::class);
Column::extend('urlWrapper', \App\Admin\Extensions\Column\UrlWrapper::class);
Column::extend('ip', \App\Admin\Extensions\Column\Ip::class);

show::extend('currency', \App\Admin\Extensions\Show\Currency::class);
show::extend('array2json', \App\Admin\Extensions\Show\Array2json::class);
show::extend('ip', \App\Admin\Extensions\Show\Ip::class);

// 排序表单
function form_sort($form)
{
    $form->number('sort', __('Sort'))->default(0)->min(0)->max(999);
}

// 启用禁用表单
function form_has_enabled($form)
{
    $form->switch('has_enabled', __('Has enabled'))->default(1);
}