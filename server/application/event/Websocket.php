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

use model\User;
use nb\event\Swoole;
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
        echo "{$name}下线了\n";
        //$this->netsplit($server,$name);
    }

    private function netsplit($server,$fd,$name) {
        $user = User::name($name);

        if($user->play == 'room') {
            //踢房间
        }

        if($user->play == 'doing') {
            //通知下线
        }

        Redis::hset('user:'.$name,['fd'=>0]);
        Redis::delete('fd:'.$fd);
    }

    public function shutdown() {
        Redis::rm('fd:*');
    }

}