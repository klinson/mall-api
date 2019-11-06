<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/11/6
 * Time: 20:43
 */

namespace App\Channels;


use Illuminate\Notifications\Notification;

class WxappChannel
{
    /**
     * 发送给定通知
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        /**
         * 标题 订单发货提醒
        服务类别{{keyword1.DATA}}
        订单编号{{keyword2.DATA}}
        物流公司{{keyword3.DATA}}
        物流单号{{keyword4.DATA}}
        收货地址{{keyword5.DATA}}
        下单时间{{keyword6.DATA}}
         */
        $wxapp = app('wechat.mini_program');
        $message = $notification->toWxapp($notifiable);
        $message['template_id'] = 'dp_tzVPp0UmJDMQG8lCf3DladAhQeUPPJVuooW0BW8s';
        $message['touser'] = $notifiable->wxapp_openid;
        $message['form_id'] = '';// TODO 实现不了。。可能要切换成公众号

        // 将通知发送给 $notifiable 实例
        $wxapp->template_message->send($message);
    }
}