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
        echo "{$fd} -- {$name}下线了\n";
        $user = \model\User::name($name);
        if($user->status === 'hall') {
            return;
        }
        $push = [
            'action'=>'push-room-quit',
            'name'=>$user->name,
            'seat'=>$user->seat
        ];
        $ser = \service\Room::withRun('unline',$user);
        $room = $ser->data;
        if($room->status === 'startd') {
            $push['action'] = 'push-user-online';
            $push['unline'] = 0;
        }

        $push = json_encode($push);
        foreach (['a','b','c'] as $v) {
            if($v === $user->seat) {
                continue;
            }
            $seat = $room->$v;
            if(!$seat) {
                echo 'continue-'.$seat['fd']."\n";
                continue;
            }
            $server->push($seat['fd'],$push);
        }

    }

    public function shutdown() {
        Redis::rm('fd:*');
    }

}