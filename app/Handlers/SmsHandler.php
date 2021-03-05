<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-7-11
 * Time: 下午5:14
 */

namespace App\Handlers;
use Aliyun\Sms\Api as Sms;
class SmsHandler
{
    protected $config;
    protected $Sms;
    protected static $instance;
    protected function __construct()
    {
        $this->config = config('alidayu');
        $this->Sms = new Sms($this->config);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 验证码发送
     * 您的验证码为：${code}，该验证码 5 分钟内有效，请勿泄漏于他人。
     * @param $mobile
     * @param $code
     * @author klinson <klinson@163.com>
     */
    public function sendVerifyCode($mobile, $code)
    {
        $this->Sms->setTemplate(['code' => $code], $this->config['template_code'])->send($mobile);
    }

}