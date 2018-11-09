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
 * Chat
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/5
 */
class Chat extends Controller {

    public function room() {
        $chat = \service\Chat::run('room',function ($msg,$code) {
            $this->error('chat-room',$code,$msg);
        });

        $this->success('发送成功','chat-room');

        $auth = Auth::init();

        $this->roompush($auth->room,$auth->seat,'chat-room',[
            'text'=>$chat->data,
            'seat'=>$auth->seat
        ]);
    }

}