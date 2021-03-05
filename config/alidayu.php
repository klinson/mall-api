<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-7-9
 * Time: 上午9:57
 */
return [
    'accessKeyId' => env('ALIDAYU_APP_KEY', ''),
    'accessKeySecret' => env('ALIDAYU_APP_SECRET', ''),
    'signName' => env('ALIDAYU_SIGN_NAME', ''),
    // 使用HTTPs
    'secure' => env('ALIDAYU_SECURE', false),
    // 沙盒环境
    'sandbox' => env('ALIDAYU_SANDBOX', false),
    // 验证短信默认模板
    'template_code' => env('ALIDAYU_TEMPLATE_CODE', ''),
];