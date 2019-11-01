<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/10/9
 * Time: 01:00
 */

namespace App\Http\Controllers\Api;


use App\Handlers\LogHandler;
use App\Models\MemberRechargeOrder;
use App\Models\Order;
use App\Models\RechargeThresholdOrder;
use Illuminate\Http\Request;
use DB;

class WechatController
{
    public function OrderPaidNotify(Request $request)
    {
        $app = app('wechat.payment');
        $response = $app->handlePaidNotify(function ($message, $fail) use ($request, $app) {
            try {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $message);

                // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $order = Order::where('order_number', $message['out_trade_no'])->first();
                if (! $order) {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '未找到对应订单');
                    return true;
                }

                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $order);
                if ($order->status == 2) { // 如果订单不存在 或者 订单已经支付过了
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '重复通知，已支付');
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                    // 用户是否支付成功
                    if (array_get($message, 'result_code') === 'SUCCESS') {

                        ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
                        $query_info = $app->order->queryByOutTradeNumber($message['out_trade_no']);
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '查询订单支付状态结果', $query_info);

                        if (
                            $query_info['return_code'] === 'SUCCESS'
                            && array_get($query_info, 'result_code') === 'SUCCESS'
                            && array_get($query_info, 'trade_state') === 'SUCCESS'
                            && array_get($query_info, 'out_trade_no') === $message['out_trade_no']
                        ) {
                            // 查询结果也是成功

                            // 检查余额
                            $res = false;
                            if ($order->used_balance) {
                                // 使用余额支付了
                                $res = true;
                            } else {
                                // 标记支付
                                $order->payed_at = date('Y-m-d H:i:s'); // 更新支付时间为当前时间
                                $order->real_cost = array_get($query_info, 'total_fee'); // 更新实际支付金额
                                $order->status = 2;
                                $order->save(); // 保存订单
                                $res = true;
                            }

                            if ($res) {
                                // 日志

                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付成功');
                            } else {
                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付失败');
                            }
                            // 返回处理完成
                            return $res;
                        } else {
                            LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-diff', '查询结果与返回结果不一致');

                            return $fail('查询结果与返回结果不一致');
                        }

                        // 用户支付失败
                    } else if (array_get($message, 'result_code') === 'FAIL') {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-fail', '支付失败');
                        return true; // 返回处理完成
                    } else {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-unknown'.'-error', '未知支付结果');
                        return $fail('未知支付结果，请稍后再通知我');
                    }
                } else {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-error', '通信失败');

                    return $fail('通信失败，请稍后再通知我');
                }
            } catch (\Exception $exception) {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-exception',  $exception->getMessage());

                return $fail($exception->getMessage());
            }
        });

        return $response;
    }

    public function RechargeThresholdOrderPaidNotify(Request $request)
    {
        $app = app('wechat.payment');
        $response = $app->handlePaidNotify(function ($message, $fail) use ($request, $app) {
            try {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $message);

                // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $order = RechargeThresholdOrder::where('order_number', $message['out_trade_no'])->first();
                if (! $order) {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '未找到对应订单');
                    return true;
                }

                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $order);
                if ($order->status == 2) { // 如果订单不存在 或者 订单已经支付过了
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '重复通知，已支付');
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                    // 用户是否支付成功
                    if (array_get($message, 'result_code') === 'SUCCESS') {

                        ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
                        $query_info = $app->order->queryByOutTradeNumber($message['out_trade_no']);
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '查询订单支付状态结果', $query_info);

                        if (
                            $query_info['return_code'] === 'SUCCESS'
                            && array_get($query_info, 'result_code') === 'SUCCESS'
                            && array_get($query_info, 'trade_state') === 'SUCCESS'
                            && array_get($query_info, 'out_trade_no') === $message['out_trade_no']
                        ) {
                            // 查询结果也是成功
                            try {
                                $order->setSuccess();
                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付成功');

                                return true;
                            } catch (\Exception $exception) {
                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付失败');
                                return $fail($exception->getMessage());
                            }

                        } else {
                            LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-diff', '查询结果与返回结果不一致');

                            return $fail('查询结果与返回结果不一致');
                        }

                        // 用户支付失败
                    } else if (array_get($message, 'result_code') === 'FAIL') {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-fail', '支付失败');
                        return true; // 返回处理完成
                    } else {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-unknown'.'-error', '未知支付结果');
                        return $fail('未知支付结果，请稍后再通知我');
                    }
                } else {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-error', '通信失败');

                    return $fail('通信失败，请稍后再通知我');
                }
            } catch (\Exception $exception) {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-exception',  $exception->getMessage());

                return $fail($exception->getMessage());
            }
        });

        return $response;
    }

    public function MemberRechargeOrderPaidNotify(Request $request)
    {
        $app = app('wechat.payment');
        $response = $app->handlePaidNotify(function ($message, $fail) use ($request, $app) {
            try {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $message);

                // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $order = MemberRechargeOrder::where('order_number', $message['out_trade_no'])->first();
                if (! $order) {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '未找到对应订单');
                    return true;
                }

                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], $order);
                if ($order->status == 2) { // 如果订单不存在 或者 订单已经支付过了
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '重复通知，已支付');
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                    // 用户是否支付成功
                    if (array_get($message, 'result_code') === 'SUCCESS') {

                        ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
                        $query_info = $app->order->queryByOutTradeNumber($message['out_trade_no']);
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '查询订单支付状态结果', $query_info);

                        if (
                            $query_info['return_code'] === 'SUCCESS'
                            && array_get($query_info, 'result_code') === 'SUCCESS'
                            && array_get($query_info, 'trade_state') === 'SUCCESS'
                            && array_get($query_info, 'out_trade_no') === $message['out_trade_no']
                        ) {
                            // 查询结果也是成功
                            try {
                                $order->setSuccess();
                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付成功');

                                return true;
                            } catch (\Exception $exception) {
                                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'], '支付失败');
                                return $fail($exception->getMessage());
                            }

                        } else {
                            LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-diff', '查询结果与返回结果不一致');

                            return $fail('查询结果与返回结果不一致');
                        }

                        // 用户支付失败
                    } else if (array_get($message, 'result_code') === 'FAIL') {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-fail', '支付失败');
                        return true; // 返回处理完成
                    } else {
                        LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-unknown'.'-error', '未知支付结果');
                        return $fail('未知支付结果，请稍后再通知我');
                    }
                } else {
                    LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-error', '通信失败');

                    return $fail('通信失败，请稍后再通知我');
                }
            } catch (\Exception $exception) {
                LogHandler::log('wechat-payment', 'notify-'.$message['out_trade_no'].'-exception',  $exception->getMessage());

                return $fail($exception->getMessage());
            }
        });

        return $response;
    }
}