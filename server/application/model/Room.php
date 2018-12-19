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
use nb\Pool;
use util\Auth;
use util\Redis;

/**
 * 游戏房间
 *
 * @package model
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/31
 */
class Room extends Model {

    /**
     * Iterator
     */
    public function current() {
        $this->row = current($this->stack);

        if(is_array($this->row)) {
            return $this;
        }
        if($this->row) {
            $row = Redis::hGetAll('room:'.$this->row);
            if($row)  {
                $this->row = $row;
            }
            else {
                $this->row = self::create($this->row);
            }
            return $this;

        }
        return false;
    }

    //创建房间

    /**
     * @param $id
     * @param bool $destroy 是否可自动销毁，针对玩家自建房间
     * @param null $name
     * @param bool $pass
     * @return Room
     */
    public static function create($id,$destroy=false,$name=null,$pass=false) {
        $name === null and $name = '房间'.$id;
        $room = [
            'id'=>$id,
            //等待wait,//开始阶段startd，//end结束清算阶段
            'status'=>'wait',
            'name'=>$name,//房间名称
            'pass'=>$pass,//房间密码
            'destroy'=>$destroy,
            //牌桌座位
            'a'=>0, //座位A
            'b'=>0, //座位B
            'c'=>0, //座位C
        ];
        Redis::hMset('room:'.$id,$room);
        return new self($room);
    }

    public static function init() {
        return Pool::value(get_called_class(),function () {
            return new self([1,2,3,4,5]);
        });
    }

    /**
     * @param $id
     * @return Room
     */
    public static function get($id) {
        $room = Redis::hGetAll('room:'.$id);
        if($room) {
            return new self($room);
        }
        return self::create($id);
    }

    //protected function _name() {
    //    return '房间'.$this->id;
    //}

    //房间玩家人数
    protected function _number() {
        $i = 0;
        foreach (['a','b','c'] as $v) {
            $this->$v and $i++;
        }
        return $i;
    }

    public function add(User $user) {
        foreach (['a','b','c'] as $v) {
            if($this->$v) {
                continue;
            }
            $seat = [
                'fd'  =>$user->fd,
                'name'=> $user->name,
                'coin'=> $user->coin,
                'ready' => 0
            ];
            $this->$v = $seat;
            //记录玩家的房间和座位
            $user->room = "{$this->id}-{$v}";
            return true;
        }
        return true;
    }

    public function del($seat) {
        Redis::hMset('room:'.$this->id,[$seat=>0]);
        return 0;
    }

    protected function _a() {
        return $this->getSeat('a');
    }

    protected function _b() {
        return $this->getSeat('b');
    }

    protected function _c() {
        return $this->getSeat('c');
    }

    protected function ___a($player) {
        return $this->setSeat('a',$player);
    }

    protected function ___b($player) {
        return $this->setSeat('b',$player);
    }

    protected function ___c($player) {
        return $this->setSeat('c',$player);
    }

    private function getSeat($seat) {
        $seat = $this->raw($seat);
        if($seat) {
            return json_decode($seat,true);
        }
        return 0;
    }

    private function setSeat($seat,$player) {
        if($player) {
            $player = array_merge($this->$seat?:[],$player);
            Redis::hMset('room:'.$this->id,[$seat=>json_encode($player)]);
            return $player;
        }
        Redis::hMset('room:'.$this->id,[$seat=>0]);
        return 0;
    }

    //底牌
    protected function _pocket() {
        return json_decode($this->raw('pocket'),true);
    }

    //出牌
    protected function _lead() {
        return json_decode($this->raw('lead'),true);
    }

    //出牌
    protected function ___lead($lead) {
        $data['lead'] = array_merge($this->lead,$lead);
        if(isset($lead['seat'])) {
            $seat = $lead['seat'];
            $data['leader'] = $seat==='a'?'b':($seat==='b'?'c':'a');
            $this->tmp('leader',$data['leader']);
        }
        $data['lead'] = json_encode($data['lead']);
        Redis::hMset('room:'.$this->id,$data);

        return $lead;
    }

    //设置地主
    protected function ___landowner($seat) {
        Redis::hMset('room:'.$this->id,[
            'landowner'=>$seat,//地主
            'leader'=>$seat,//当前需要出牌的玩家
            'lead'=>json_encode([
                'seat'=>$seat,
            ]), //出牌记录
            'win'=>0  //最先出完牌的人
        ]);
        return $seat;
    }

    //
    protected function ___call($call) {
        Redis::hmset('room:'.$this->id,['call'=>json_encode($call)]);
        return $call;
    }

    protected function _call() {
        return json_decode($this->raw('call'),true);
    }

    protected function ___win($seat) {
        Redis::hmset('room:'.$this->id,[
            'win'=>$seat,
            'leader'=>0
        ]);
        $this->tmp['leader'] = 0;
        return $seat;
    }

    protected function ___leader($seat) {
        Redis::hMset('room:'.$this->id,[
            'leader'=>$seat
        ]);
        return $seat;
    }


    //销毁房间
    public function destroy() {
        Redis::delete('room:'.$this->id);
        $this->destroy = true;
        return true;
    }

}