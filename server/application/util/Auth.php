<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace util;

use model\Room;
use model\User;
use nb\Pool;
use nb\Server;

/**
 * Auth
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class Auth extends User {

    public static function init($token=null) {
        return Pool::value(get_called_class(),function () use ($token) {
            $name =  Redis::get('token:'.$token);
            if(!$name) {
                //没有登录信息
                return new self();
            }
            $auth = self::name($name);
            $fd = Server::driver()->fd;
            if($auth->fd === $fd) {
                return $auth;
            }
            $auth->fd = $fd;
            Redis::set('fd:'.$fd,$name);

            //如果在房间
            if(!$auth->roomid) {
                return $auth;
            }

            $seat = $auth->seat;
            $room = $auth->room;
            $room->$seat = ['fd'=>$fd];

            $auth->online = 1;//设置为在线状态

            $push = json_encode([
                'action'=>'push-user-online',
                'msg'=>'上线了',
                'name'=>$auth->name,
                'seat'=>$auth->seat
            ]);
            foreach (['a','b','c'] as $v) {
                if($v === $seat) {
                    continue;
                }
                $seat = $room->$v;
                if(!$seat) {
                    continue;
                }
                Server::driver()->push($seat['fd'],$push);
            }
            return $auth;

        });
    }


}