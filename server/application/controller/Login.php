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

use nb\Response;
use nb\Server;
use util\Base;

/**
 * Auth
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class Login extends Base {

    protected $rule = [
        'name'  =>  'require|max:25',
        'pass'  =>  'require|max:25',
    ];

    public function __error($msg,$args) {
        $this->error(402,$msg);
    }

    //登录
    public function index() {
        Response::header('Access-Control-Allow-Origin', '*');
        Response::header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, PATCH, OPTIONS');
        Response::header('Access-Control-Allow-Headers', 'Authorization, User-Agent, Keep-Alive, Content-Type, X-Requested-With');
        $run = \service\User::run('login',function ($msg,$code) {
            $this->error($code,$msg);
        });

        $this->success($run->msg,[
            'token'=>$run->data
        ]);
    }


    //登录
    public function index_ws() {

        $run = \service\User::run('login',function ($msg) {
            $this->end($msg);
        });
        $this->reply($run->msg);
    }

    //重连
    public function reconnection() {
        $run = \service\User::run('reconnection',function ($msg) {
            $this->end($msg);
        });
        $this->reply($run->msg);
    }

    //断线
    public function out() {
        Server::driver()->close();
    }

}