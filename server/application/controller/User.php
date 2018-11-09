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
 * User
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class User extends Controller {

    public function index() {
        $auth = Auth::init();

        $this->success('获取成功','user',[
             "name"=>$auth->name,
             "coin"=>$auth->coin,
             "play"=>$auth->play,
             "room"=>$auth->roomid,
             "seat"=>$auth->seat,
        ]);
    }


}