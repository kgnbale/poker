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
            if($auth->fd !== $fd) {
                $auth->fd = $fd;
                Redis::set('fd:'.$fd,$name);

                //如果在房间
                if($auth->roomid) {
                    $seat = $auth->seat;
                    $room = $auth->room;
                    $room->$seat = ['fd'=>$fd];
                }
            }
            return $auth;
        });
    }


}