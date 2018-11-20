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

use util\Auth;
use util\Controller;
/**
 * Room
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/1
 */
class Room extends Controller {

    public function index() {
        $room = \model\Room::init();
        $rooms = [];
        foreach ($room as $v) {
            $a = $v->a;$b = $v->b;$c = $v->c;
            $rooms[] = [
                'id'=>$v->id,
                'name'=>$v->name,
                'status'=>$v->status,
                'number'=>$v->number,
                'a'=>$a?$a['name']:0,
                'b'=>$b?$b['name']:0,
                'c'=>$c?$c['name']:0,
            ];
        }
        $this->success('获取成功','room',[
            'rooms'=>$rooms
        ]);
    }

    //进入房间
    public function enter() {
        $run = \service\Room::run('enter',function ($msg,$code) {
            $this->error('room-enter',$code,$msg);
        });

        $play = Auth::init();

        $room = $run->data;
        $this->success('进入房间','room-enter',[
            'id'=>$room->id,
            'name'=>$room->name,
            'status'=>$room->status,
            'seat'=>$play->seat, //我的座位
            'a'=>$room->a,
            'b'=>$room->b,
            'c'=>$room->c
        ]);

        //通知房间其他人，有玩家加入了
        $name = $play->name;

        $this->roompush($room,$play->seat,'room-enter',[
            'msg'=>"{$name}进入房间",
            'name'=>$name,
            'coin'=>$play->coin,
            'seat'=>$play->seat
        ]);
    }

    //同步房间信息
    public function synchro() {
        $run = \service\Room::run('synchro',function ($msg,$code) {
            $this->error('room-synchro',$code,$msg);
        });
        $auth = Auth::init();
        $room = $run->data;
        $data = [
            'name'=>$room->name,
            'status'=>$room->status,
        ];
        foreach (['a','b','c'] as $v) {
            $seat = $room->$v;
            if(!$seat) {
                $data[$v] = [];
                continue;
            }

            if($v === $auth->seat) {
                $data[$v] = $seat;
                continue;
            }

            $data[$v] = [
                'coin'=>$seat['coin'],
                'name'=>$seat['name'],
                'ready'=>$seat['ready'],
                'lead'=>$seat['lead']
            ];
        }
        if($room->status === 'startd') {
            $data['landowner'] = $room->landowner?:0;
            if($data['landowner']) {
                foreach ($room->pocket as $v) {
                    $data['pocket'][] = $v;
                }
                $data['leader'] = $room->leader;//当前该谁出牌
                //当前需要大过的牌
                $lead = $room->lead;
                if(isset($lead['is'])) {
                    $data['lead'] = [
                        'type'=>$lead['is'],
                        'max'=>$lead['max'],
                        'num'=>$lead['num'],
                        'poker'=>$lead['poker'],
                        'seat'=>$lead['seat'],
                    ];
                }
                else{
                    $data['lead'] = [
                        'seat'=>$lead['seat'],
                        'poker'=>[],
                    ];
                }
            }
            else {
                $data['call'] = $room->call['who'];
            }
        }
        $this->success('获取成功','room-synchro',$data);
    }

    //退出房间
    public function quit() {
        $auth = Auth::init();
        $seat = $auth->seat;

        $run = \service\Room::run('quit',function ($msg,$code) {
            $this->error('room-quit',$code,$msg);
        });

        $room = $run->data;
        $this->success('退出成功','room-quit');

        //通知房间其它人
        $this->roompush($room,0,'room-quit',[
            'name'=>$auth->name,
            'seat'=>$seat
        ]);
    }


}