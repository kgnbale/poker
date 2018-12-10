<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace model;
use nb\Model;
use nb\Server;
use util\Redis;

/**
 * User
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class User extends Model {
    /**
     * 根据用户获取用户信息
     */
    public static function name($name) {
        $user = Redis::hGetAll('user:'.$name);
        if($user) {
            return new self($user);
        }
        $user = self::find('name=?',$name);

        if($user->have) {
            $data = $user->stack();
            //游戏状态分为大厅hall，房间room，准备ready，大牌中doing
            //游戏状态初始为大厅状态
            $data['play'] = 'hall';
            $data['roomid'] = 0;//所在房间ID，默认为0，不在房间
            $data['seat'] = 0;
            Redis::hMset('user:'.$name,$data);
        }
        return $user;
    }

    /**
     * 更新token
     * @param $name
     * @param $token
     */
    public static function uptoken($name,$token) {
        Redis::hset('user:'.$name,'token',$token);
    }


    //修改token
    protected function ___token($token) {
        Redis::hset('user:'.$this->name,'token',$token);
        return $token;
    }

    //修改fd
    protected function ___fd($fd) {
        Redis::hset('user:'.$this->name,'fd',$fd);
        return $fd;
    }

    protected function _room() {
        return Room::get($this->roomid);
    }

    // 设置用户的房间号
    // 1-a 表示房间1，a号位
    // 0-0 表示离开房间
    protected function ___room($room) {
        list($roomid,$seat) = explode('-',$room);
        Redis::hmset('user:'.$this->name,[
            'roomid'=>$roomid,
            'seat'  =>$seat,
            'play'  =>$roomid?'room':'hall'
        ]);
        $this->seat = $seat;
        $this->roomid = $roomid;
    }

    protected function ___coin($coin) {
        $coin = $this->coin + $coin;
        $row = self::updateId($this->id,'coin=?',$coin);
        $row and Redis::hmset('user:'.$this->name,[
            'coin'=>$coin
        ]);
        return $coin;
    }

    protected function ___online($flag) {
        Redis::hmset('user:'.$this->name,[
            'online'=>$flag
        ]);
    }

}