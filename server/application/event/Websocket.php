<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace event;

use model\Room;
use model\User;
use nb\event\Swoole;
use nb\Server;
use util\Redis;

/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 17/12/2 下午4:46
 */
class Websocket extends Swoole {

    public function connect($server, $fd) {
        echo "connection open: {$fd}\n";
    }


    public function close($server, $fd) {
        $name = Redis::get('fd:'.$fd);
        if(!$name) {
            echo "connection-close: {$fd}\n";
            return false;
        }
        $user = \model\User::name($name);
        if($user->status === 'hall') {
            echo "{$name}下线了\n";
            return;
        }
        $push = [
            'name'=>$user->name,
            'seat'=>$user->seat
        ];
        $room = \service\Room::withRun('unline',$user);

        $action = 'push-room-quit';

        if($room->status === 'startd') {
            $action = 'push-user-online';
            $push['unline'] = 0;
        }

        foreach (['a','b','c'] as $v) {
            if($v === $user->seat) {
                continue;
            }
            $seat = $room->$v;
            if(!$seat) {
                continue;
            }
            Server::driver()->push($seat['fd'],$action,$push);
        }
    }

    public function shutdown() {
        Redis::rm('fd:*');
    }

}