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

use model\Poker;
use nb\Response;
use service\Game;
use util\Auth;
use util\Controller;

/**
 * Play
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/31
 */
class Play extends Controller {

    //准备游戏
    public function ready() {
        $game = Game::run('ready',function ($msg,$code) {
            $this->error('play-ready',$code,$msg);
        });

        $seat = Auth::init()->seat;
        $room = $game->data;
        $ready = $room->$seat['ready'];

        $this->success($ready?'准备':'取消准备','play-ready',[
            'ready'=>$ready
        ]);

        //通知给其它玩家
        //且检查如果所有人都是准备状态，将开始游戏
        $i = 0;
        foreach (['a','b','c'] as $v) {
            $player = $room->$v;
            if(!$player) {
                continue;
            }
            $player['ready'] and $i++;
            if($v === $seat) {
                continue;
            }
            $this->push($player['fd'],'play-ready',[
                'seat'=>$seat,
                'ready'=>$ready
            ]);
        }
        $this->start($i,$room);
    }

    private function start($i,$room) {
        if($i !== 3) {
            return false;
        }
        $play = Game::withRun('start',$room,function ($msg,$code) {
            $this->error('play-ready',$code,$msg);
        });

        $room = $play->data;
        $call = $room->call['who'];
        foreach (['a','b','c'] as $v) {
            $player = $room->$v;
            $this->push($player['fd'],'play-start',[
                'call'=>$call,
                'poker'=>array_values($player['poker'])
            ]);
        }
    }

    //抢地主
    public function rob() {
        $rob = Game::run('rob',function ($msg,$code) {
            $this->error('play-rob',$code,$msg);
        });

        $room = $rob->data;
        $call = $room->call['who'];
        $this->success('抢地主','play-rob',[
            'call' => $call
        ]);

        $landowner = $room->landowner?:0;

        $seats = ['a','b','c'];
        //如果地主没有产生，则只通知另外两个玩家
        $landowner or $seats = array_diff($seats, [Auth::init()->seat]);

        foreach ($seats as $v) {
            $player = $room->$v;
            $push = [];
            $push['landowner'] = $landowner;
            $push['call'] = $call;
            if($landowner) {
                foreach ($room->pocket as $p) {
                    $push['pocket'][] = $p;
                }
                $landowner === $v and $push['poker'] = $player['poker'];
            }
            $this->push($player['fd'],'play-rob',$push);
        }
    }

    //出牌
    public function lead() {
        $lead = Game::run('lead',function ($msg,$code) {
            $this->error('play-lead',$code,$msg);
        });

        $seat = Auth::init()->seat;
        $room = $lead->data;
        $player = $room->$seat;
        $residue = count($player['poker']);
        $this->success('出牌','play-lead',[
            'nexter'=>$room->leader,
            'type'=>$room->lead['is'],
            'residue'=>$residue
        ]);


        $this->roompush($room,$seat,'play-lead',[
            'seat'=>$seat,
            'poker'=>$player['lead'],
            'nexter'=>$room->leader,
            'type'=>$room->lead['is'],
            'residue'=>$residue
        ]);

        $this->end($room);
    }

    private function end($room) {
        $win = $room->win;
        if(!$win) {
            return false;
        }

        $end = Game::withRun('end',[$room]);

        $push = $end->data;

        //平民和地主
        foreach (['a','b','c'] as $v) {
            $player = $room->$v;
            $player = \model\User::name($player['name']);
            $player->coin = $push[$v];
            $this->push($player['fd'],'play-end',$push);
        }
    }

}