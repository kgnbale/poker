<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace controller;

use model\Order;
use nb\Request;
use nb\Server;
use util\Base;

/**
 * Call
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/22
 */
class Call extends Base {

    public function index() {
        /**
         * 支付通知验签demo
         */
        $pay = Request::form('post');
        /**
         * 注意：$_POST数据如果服务器没有自动处理urldecode，请做一次urldecode(参考rfc1738标准)处理
         */
        /**
        foreach ($data as $key => $value) {
            $data[$key] = urldecode($value);
        }
        **/
        $privateKey = "555FAA0B1628AA5D90D404FBAE9C1F0C";
        //$enhancedKey = 'OGM3ODFkNDRhYjUzYjM4ZmUzZjk';
        //注意：如果没有增强密钥的游戏只需要通用验签即可，即只需要checkSign
        if ($this->checkSign($pay, $privateKey)) {
        //if (!$this->checkSign($pay, $privateKey) || !$this->checkEnhancedSign($pay, $enhancedKey)) {
            echo "failed";
            return;
        }
        $post = json_encode($pay);
        $time = time();

        //支付用户信息
        $user = \model\User::name($pay['user_id']);

        $serial = md5($user->id.$pay['order_id']);
        $order = Order::find('serial=?',$serial);
        if($order->empty) {
            $order = [
                'serial'=>$serial,
                'order_id'=>$pay['order_id'],
                'order_status'=>$pay['status'],
                'uid'=>$user->id,
                'number'=>$pay['product_count'],
                'product_id'=>$pay['product_id'],
                'status'=>1,
                'post'=>$post,
                'ct'=>$time,
                'ut'=>$time
            ];
            $order['id'] = Order::insert($order);
        }

        //支付未完成
        //支付状态，1 为成功，非1则为其他异常状态，游服请在成功的状态下发货
        if($pay->status !== 1 || $user->empty) {
            echo "failed";
            return;
        }

        $plist = load('product');
        $local = $plist[$pay['product_id']];

        //支付金额异常
        //支付金额，单位元 值根据不同渠道的要求可能为浮点类型
        if($local['price'] * $pay['product_count'] !== $pay['amount']) {
            echo "failed";
            return;
        }

        //发放奖励
        $user->coin = $local['coin'] * $pay['product_count'];

        //完成订单
        Order::updateId($order['id'],[
            'status'=>0,
            'post'=>$post,
            'ct'=>$time
        ]);

        //如果用户在线
        //推送支付成功信息
        Server::driver()->push($user->fd,json_encode([
            "action"=>"push-pay",
            "msg"=> "你的购买已完成",
            'serial'=>$serial,
            'order_id'=>$pay['order_id'],
            'status'=>0,
        ]));
        echo "ok";
    }


    /**
     * 通用验签
     * @param array $data 接收到的所有请求参数数组，通过$_POST可以获得。注意data数据如果服务器没有自动解析，请做一次urldecode(参考rfc1738标准)处理
     * @param array $privateKey AnySDK分配的游戏privateKey
     * @return bool
     */
    private function checkSign($data, $privateKey) {
        if (empty($data) || !isset($data['sign']) || empty($privateKey)) {
            return false;
        }
        $sign = $data['sign'];
        //sign 不参与签名
        unset($data['sign']);
        $_sign = $this->getSign($data, $privateKey);
        if ($_sign != $sign) {
            return false;
        }
        return true;
    }

    /**
     * 增强验签
     * @param type $data
     * @param type $enhancedKey
     * @return boolean
     */
    private function checkEnhancedSign($data, $enhancedKey) {
        if (empty($data) || !isset($data['enhanced_sign']) || empty($enhancedKey)) {
            return false;
        }
        $enhancedSign = $data['enhanced_sign'];
        //sign及enhanced_sign 不参与签名
        unset($data['sign'], $data['enhanced_sign']);
        $_enhancedSign = $this->getSign($data, $enhancedKey);
        if ($_enhancedSign != $enhancedSign) {
            return false;
        }
        return true;
    }

    /**
     * 计算签名
     * @param array $data
     * @param string $key
     * @return string
     */
    private function getSign($data, $key) {
        //数组按key升序排序
        ksort($data);
        //将数组中的值不加任何分隔符合并成字符串
        $string = implode('', $data);
        //做一次md5并转换成小写，末尾追加游戏的privateKey，最后再次做md5并转换成小写
        return strtolower(md5(strtolower(md5($string)) . $key));
    }

    //商品列表接口
    public function product() {
        $plist = load('product');

        $data = [];
        foreach ($plist as $k=>$v) {
            $v['id'] = $k;
            $data[] = $v;
        }

        $this->success('获取成功',[
            'product'=>$data
        ]);
    }

}