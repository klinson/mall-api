<?php

namespace App\Notifications;

use App\Channels\WxappChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderExpress extends Notification
{
    use Queueable;

    protected $order;
    /**
     * 订单发货通知
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WxappChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


    public function toWxapp($notifiable)
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
        return [
            'page' => 'pages/index/index',
            'data' => [
                'keyword1' => '商场订单',
                'keyword2' => $this->order->order_number,
                'keyword3' => $this->order->express->name ?? '无需快递',
                'keyword4' => $this->order->express_number ?: '',
                'keyword5' => $this->order->address_snapshot['city_name'] . ' '.$this->order->address_snapshot['address'],
                'keyword6' => $this->order->created_at->toDateTimeString(),
            ],
        ];
    }
}
