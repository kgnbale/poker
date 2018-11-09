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

        $landowner = $room->landowner;

        $seats = ['a','b','c'];
        //如果地主没有产生，则只通知另外两个玩家
        $landowner or $seats = array_diff($seats, [Auth::init()->seat]);

        foreach ($seats as $v) {
            $player = $room->$v;
            $push = [];
            $push['landowner'] = $landowner;
            $push['call'] = $call;
            if($landowner) {
                $push['pocket'] = $room->pocket;
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
        $this->success('出牌','play-lead',[
            'nexter'=>$room->leader,
            'win'=>$room->win
        ]);

        $player = $room->$seat;

        $this->roompush($room,$seat,'play-lead',[
            'seat'=>$seat,
            'poker'=>$player['lead'],
            'nexter'=>$room->leader
        ]);

        $this->end($seat, $room);
    }

    private function end($room) {
        $win = $room->win;
        if(!$win) {
            return false;
        }
        //低分
        $score = 3;

        //地主
        $landowner = $room->landowner;

        //胜利者
        $winer = $room->$win;

        //农民
        $farmer = $landowner==='a'?['b','c']:($landowner==='b'?['a','c']:['a','b']);

        $push = [];

        if($win === $room->landowner) {
            $push['landowner']=1;
            $winscore = 0;
            foreach ($farmer as $v) {
                $loser = $room->$v;
                $tmp = $loser['multiple'] * $winer['multiple'] * $score;
                $push[$v]= -$tmp;
                $winscore +=  $tmp;
            }
            $push[$landowner] = $winscore;
        }
        else{
            $push['landowner']=0;
            $winscore = 0;
            foreach ($farmer as $v) {
                $loser = $room->$v;
                $tmp = $loser['multiple'] * $winer['multiple'] * $score;
                $push[$v]= $tmp;
                $winscore -=  $tmp;
            }
            $push[$landowner] = $winscore;
        }

        //平民和地主
        foreach (['a','b','c'] as $v) {
            $player = $room->$v;
            $player = \model\User::name($player['name']);
            $player->coin = $push[$v];
            $this->push($player['fd'],'play-end',$push);
        }
    }

    //出牌
    public function lead2($poker) {
        $poker = explode(',',$poker);
        $poker = new Poker($poker);
        $map = [
            'individual'=>'个',
            'straight'=>'顺子',
            'straights'=>'顺对',
            'bomb'=>'炸弹',
            'nbomb'=>'核弹',
            'couplet'=>'对子',
            'plane'=>'飞机',
            'train'=>'坦克'
        ];

        if(isset($map[$poker->is])) {
            $this->success($map[$poker->is],'play-lead');
        }
        else {
            $this->error('play-lead',4033,'出牌失败！');
        }
    }


    public function test2() {
        Response::header("Content-Type", "text/html; charset=utf-8");
        $poker = '6,7,8,10,11,13,14,15,16,34,35,37';
        $poker = explode(',',$poker);
        $poker = new Poker($poker);
        echo $poker->is?:'出牌失败';
    }

}