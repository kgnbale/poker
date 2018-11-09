<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace service;
use nb\Service;
use util\Auth;
use util\Redis;

/**
 * Room
 *
 * @package service
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/1
 */
class Room extends Service {

    public function enter() {
        $auth = Auth::init();

        if($auth->play !== 'hall') {
            $this->code = 4031;
            $this->msg = '操作不允许';
            return false;
        }

        $id = $this->input('id');
        $id = $id?:$this->random();
        if($id === 0) {
            $this->code = 409;
            $this->msg = '房间满了';
            return false;
        }

        $room = \model\Room::get($id);

        if($room->number > 2) {
            $this->code = 409;
            $this->msg = '房间满了';
            return false;
        }

        $room->add($auth);
        $this->data = $room;
        return true;
    }

    //退出房间
    public function quit() {
        $auth = Auth::init();

        if($auth->play === 'hall') {
            $this->code = 4032;
            $this->msg = '你已经在大厅了';
            return false;
        }

        $room = $auth->room;

        if($room->status === 'startd') {
            $this->code = 4033;
            $this->msg = '你必须等到本局游戏结束才可以退出房间！';
            return false;
        }

        $seat = $auth->seat;

        //设置玩家的房间为0，表示退出房间
        $auth->room = '0-0';

        //清楚玩家在游戏房间的位置信息
        $room->$seat = 0;

        $this->data = $room;
        return true;
    }


    public function synchro() {
        $auth = Auth::init();

        if($auth->play === 'hall') {
            $this->code = 4031;
            $this->msg = '不在房间';
            return false;
        }

        $this->data = $auth->room;
        return true;
    }

    private function random() {
        $stack = [];
        $room = \model\Room::init();
        foreach ($room as $v) {
            if($v->playerNum == 2) {
                return $v->id;
            }
            $stack[$v->playerNum] = $v->id;
        }

        //如果有一人的房间
        if(isset($stack[1])) {
            return $stack[1][0];
        }

        //如果有没有人的房间
        if(isset($stack[0])) {
            return $stack[0][0];
        }

        return 0;
    }

}