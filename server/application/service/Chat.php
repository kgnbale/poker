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

/**
 * Chat
 *
 * @package service
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/6
 */
class Chat extends Service {

    public function room() {
        $auth = Auth::init();

        if($auth->roomid===0) {
            $this->code = 500;
            $this->msg = '不在游戏房间！';
            return false;
        }

        //可以添加敏感字过滤
        $text = $this->input('text');

        $this->data = $text;
        return true;
    }

}